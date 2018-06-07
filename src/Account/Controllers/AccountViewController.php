<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Account\Forms\RegistrationFormType;

class AccountViewController extends AbstractController
{
    /**
     * @var RegistrationFormType
     */
    private $registrationFormType;

    /**
     * @var TranslatorInterface
     */
    private $tranlator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->tranlator = $translator;
        $this->registrationFormType = new RegistrationFormType();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $form = $this->createRegisterForm();
        $form->handleRequest($request);

        return $this->render(
            'accounts/register.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @return FormInterface
     */
    private function createRegisterForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->registrationFormType->buildForm(
          $formBuilder,
          [
              'translator' => $this->tranlator,
          ]
        );

        return $formBuilder->getForm();
    }
}
