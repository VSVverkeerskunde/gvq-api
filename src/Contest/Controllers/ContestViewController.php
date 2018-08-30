<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Contest\Forms\ContestFormType;
use VSV\GVQ_API\Contest\Models\TieBreaker;
use VSV\GVQ_API\Contest\Repositories\TieBreakerRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;

class ContestViewController extends AbstractController
{
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
     * @param TieBreakerRepository $tieBreakerRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TieBreakerRepository $tieBreakerRepository,
        TranslatorInterface $translator
    ) {
        $this->tieBreakerRepository = $tieBreakerRepository;
        $this->translator = $translator;

        $this->contestFormType = new ContestFormType();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function contest(Request $request): Response
    {
        $form = $this->createContestForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        // TODO: Inject year or create ContestService.
        $tieBreakers = $this->tieBreakerRepository->getAllByYear(new Year(2018));

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
