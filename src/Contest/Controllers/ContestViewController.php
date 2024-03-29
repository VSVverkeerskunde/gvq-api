<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Controllers;

use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Common\CsvResponse;
use VSV\GVQ_API\Contest\ContestParticipationCsvData;
use VSV\GVQ_API\Contest\Forms\ContestFormType;
use VSV\GVQ_API\Contest\Models\TieBreaker;
use VSV\GVQ_API\Contest\Repositories\TieBreakerRepository;
use VSV\GVQ_API\Contest\Service\ContestService;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class ContestViewController extends AbstractController
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @var ContestService
     */
    private $contestService;

    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @var TieBreakerRepository
     */
    private $tieBreakerRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ContestFormType
     */
    private $contestFormType;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var QuestionResultRepository
     */
    private $questionResultRepository;

    /**
     * @param Year $year
     * @param ContestService $contestService
     * @param UuidFactoryInterface $uuidFactory
     * @param QuizRepository $quizRepository
     * @param TieBreakerRepository $tieBreakerRepository
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
     * @param QuestionResultRepository $questionResultRepository
     */
    public function __construct(
        Year $year,
        ContestService $contestService,
        UuidFactoryInterface $uuidFactory,
        QuizRepository $quizRepository,
        TieBreakerRepository $tieBreakerRepository,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer,
        ResponseFactory $responseFactory,
        QuestionResultRepository $questionResultRepository
    ) {
        $this->year = $year;
        $this->contestService = $contestService;
        $this->uuidFactory = $uuidFactory;
        $this->quizRepository = $quizRepository;
        $this->tieBreakerRepository = $tieBreakerRepository;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
        $this->questionResultRepository = $questionResultRepository;

        $this->contestFormType = new ContestFormType();
    }

    /**
     * @param Request $request
     * @param string $quizId
     * @return Response
     * @throws \Exception
     */
    public function contest(Request $request, string $quizId): Response
    {
        try {
            $quiz = $this->quizRepository->getById(Uuid::fromString($quizId));

            $canParticipate = $this->contestService->canParticipate(
                $this->year,
                Uuid::fromString($quizId)
            );
        } catch (Exception $e) {
            return $this->render('contest/contest_gone.html.twig');
        }

        if (!$canParticipate) {
            return new Response('Can\'t participate', Response::HTTP_FORBIDDEN);
        }

        // We need to show the score and total questions at top of the contest form.
        $questionResult = $this->questionResultRepository->getById(
            Uuid::fromString($quizId)
        );

        $totalQuestions = $quiz->getQuestions()->count();
        $score = $questionResult->getScore();

        if ($this->contestService->hasParticipatedBefore($this->year, Uuid::fromString($quizId))) {
            return $this->render(
                'contest/contest_success.html.twig',
                [
                    'score' => $score,
                    'totalQuestions' => $totalQuestions,
                ]

            );
        }

        $form = $this->createContestForm(false);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $contestParticipation = $this->contestFormType->newContestParticipationFromData(
                $this->uuidFactory,
                $this->year,
                $quiz->getLanguage(),
                $quiz->getChannel(),
                $quiz->getParticipant()->getEmail(),
                $data
            );

            $this->contestService->save($contestParticipation);

            return $this->render('contest/contest_success.html.twig');
        } elseif ($form->isSubmitted() && $request->query->has('debug')) {
            /** @var FormError[] $errors */
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                var_dump($error->getMessage());
            }
            exit;
        }

        $tieBreakers = $this->getTieBreakerByLocaleAndChannel($request->getLocale(), $quiz->getChannel());
        $privacy_pdf = $this->generatePrivacyPdfUrl($request->getLocale());

        return $this->render(
            'contest/contest.html.twig',
            [
                'score' => $score,
                'totalQuestions' => $totalQuestions,
                'form' => $form->createView(),
                'tieBreaker1' => $tieBreakers[0],
                'tieBreaker2' => $tieBreakers[1],
                'privacy_pdf' => $privacy_pdf,
            ]
        );
    }

    /**
     * @return StreamedResponse
     */
    public function export(): Response
    {
        $rows = $this->contestService->getTraversableContestParticipations();

        $csvData = new ContestParticipationCsvData($rows, $this->serializer);

        $now = new \DateTime();
        $response = new CsvResponse(
            'contest_participations_' . $now->format(\DateTime::ATOM) . '.csv',
            $csvData->rows()
        );

        return $response;
    }

    /**
     * @return StreamedResponse
     */
    public function exportCupTeam(string $teamId): Response
    {
        $teamId = Uuid::fromString($teamId);
        $rows = $this->contestService->getContestParticipantsInTeam($teamId);

        $csvData = new ContestParticipationCsvData($rows, $this->serializer);

        $now = new \DateTime();
        $response = new CsvResponse(
            'contest_participations_' . $teamId->toString() . '_' . $now->format(\DateTime::ATOM) . '.csv',
            $csvData->rows()
        );

        return $response;
    }

    /**
     * @return FormInterface
     */
    private function createContestForm(bool $association): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->contestFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
                'association' => $association,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @param string $locale
     * @return string
     */
    private function generatePrivacyPdfUrl(string $locale): string
    {
        return $this->urlGenerator->generate(
            $locale === 'nl' ? 'privacy_pdf_nl' : 'privacy_pdf_fr',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $locale
     * @param QuizChannel $channel
     * @return TieBreaker[]
     */
    private function getTieBreakerByLocaleAndChannel(string $locale, QuizChannel $channel): array
    {
        if (!$channel->equals(new QuizChannel(QuizChannel::LEAGUE))) {
            $channel = new QuizChannel(QuizChannel::INDIVIDUAL);
        }

        $tieBreakers = $this->tieBreakerRepository->getAllByYear($this->year);

        $tieBreakersArray = [];

        if ($tieBreakers !== null) {
            /** @var TieBreaker $tieBreaker */
            foreach ($tieBreakers as $tieBreaker) {
                if ($tieBreaker->getLanguage()->toNative() === $locale &&
                    $tieBreaker->getChannel()->equals($channel)) {
                    $tieBreakersArray[] = $tieBreaker;
                }
            }
        }

        return $tieBreakersArray;
    }
}
