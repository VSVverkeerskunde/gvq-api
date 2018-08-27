<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Contest\Forms\ContestFormType;

class ContestViewController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ContestFormType
     */
    private $contestFormType;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
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

        return $this->render(
            'contest/contest.html.twig',
            [
                'form' => $form->createView(),
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
}
