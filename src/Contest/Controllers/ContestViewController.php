<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Controllers;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Contest\Forms\ContestFormType;
use VSV\GVQ_API\Contest\Models\TieBreaker;
use VSV\GVQ_API\Contest\Repositories\TieBreakerRepository;
use VSV\GVQ_API\Contest\Service\ContestService;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;

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
     * @var ContestFormType
     */
    private $contestFormType;

    /**
     * @param Year $year
     * @param ContestService $contestService
     * @param UuidFactoryInterface $uuidFactory
     * @param QuizRepository $quizRepository
     * @param TieBreakerRepository $tieBreakerRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Year $year,
        ContestService $contestService,
        UuidFactoryInterface $uuidFactory,
        QuizRepository $quizRepository,
        TieBreakerRepository $tieBreakerRepository,
        TranslatorInterface $translator
    ) {
        $this->year = $year;
        $this->contestService = $contestService;
        $this->uuidFactory = $uuidFactory;
        $this->quizRepository = $quizRepository;
        $this->tieBreakerRepository = $tieBreakerRepository;
        $this->translator = $translator;

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
        $canParticipate = $this->contestService->canParticipate(
            $this->year,
            Uuid::fromString($quizId)
        );

        if (!$canParticipate) {
            return new Response('Can\'t participate', Response::HTTP_FORBIDDEN);
        }

        $form = $this->createContestForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $quiz = $this->quizRepository->getById(Uuid::fromString($quizId));
            $contestParticipation = $this->contestFormType->newContestParticipationFromData(
                $this->uuidFactory,
                $this->year,
                $quiz->getChannel(),
                $quiz->getParticipant()->getEmail(),
                $data
            );

            $this->contestService->save($contestParticipation);

            return new Response(
                'Participation saved '.$contestParticipation->getId()->toString(),
                Response::HTTP_CREATED
            );
        }

        $tieBreakers = $this->getTieBreakerByLocale($request->getLocale());

        return $this->render(
            'contest/contest.html.twig',
            [
                'form' => $form->createView(),
                'tieBreaker1' => $tieBreakers[0],
                'tieBreaker2' => $tieBreakers[1],
            ]
        );
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
     * @return TieBreaker[]
     */
    private function getTieBreakerByLocale(string $locale): array
    {
        $tieBreakers = $this->tieBreakerRepository->getAllByYear($this->year);

        $tieBreakersArray = [];

        if ($tieBreakers !== null) {
            /** @var TieBreaker $tieBreaker */
            foreach ($tieBreakers as $tieBreaker) {
                if ($tieBreaker->getLanguage()->toNative() === $locale) {
                    $tieBreakersArray[] = $tieBreaker;
                }
            }
        }

        return $tieBreakersArray;
    }
}
