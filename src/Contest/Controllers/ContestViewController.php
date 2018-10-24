<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Controllers;

use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Contest\Forms\ContestFormType;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\TieBreaker;
use VSV\GVQ_API\Contest\Repositories\TieBreakerRepository;
use VSV\GVQ_API\Contest\Service\ContestService;
use VSV\GVQ_API\Question\ValueObjects\Year;
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
     * @param Year $year
     * @param ContestService $contestService
     * @param UuidFactoryInterface $uuidFactory
     * @param QuizRepository $quizRepository
     * @param TieBreakerRepository $tieBreakerRepository
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
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
        ResponseFactory $responseFactory
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

        $form = $this->createContestForm();
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
        }

        $tieBreakers = $this->getTieBreakerByLocaleAndChannel($request->getLocale(), $quiz->getChannel());
        $privacy_pdf = $this->generatePrivacyPdfUrl($request->getLocale());

        return $this->render(
            'contest/contest.html.twig',
            [
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
        $traversableContestParticipations = $this->contestService->getTraversableContestParticipations();

        $callback = $this->createCallBackForStreamedCsvResponse($traversableContestParticipations);
        $response = $this->responseFactory->createStreamedCsvResponse($callback, 'contest_participations');

        return $response;
    }

    /**
     * @param \Traversable $traversableContestParticipations
     * @return \Closure
     */
    private function createCallBackForStreamedCsvResponse(\Traversable $traversableContestParticipations
    ): \Closure {
        return function () use ($traversableContestParticipations) {
            $handle = fopen('php://output', 'r+');
            fwrite($handle, 'sep=,'.PHP_EOL);

            $headerSet = false;
            foreach ($traversableContestParticipations as $contestParticipation) {
                /** @var ContestParticipation $contestParticipation */

                $contestParticipationAsCsv = $this->serializer->serialize(
                    $contestParticipation,
                    'csv'
                );
                $headerValuesArray = explode("\n", $contestParticipationAsCsv);

                if (!$headerSet) {
                    $header = $headerValuesArray[0];
                    fwrite($handle, $header.PHP_EOL);
                    $headerSet = true;
                }

                fwrite(
                    $handle,
                    $headerValuesArray[1].PHP_EOL
                );
            }
            fclose($handle);
        };
    }

    /**
     * @return FormInterface
     */
    private function createContestForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->contestFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
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
        if (!$channel->equals(new QuizChannel(QuizChannel::CUP))) {
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
