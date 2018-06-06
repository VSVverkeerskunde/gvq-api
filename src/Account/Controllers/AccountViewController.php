<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Account\Forms\RegistrationFormType;

class AccountViewController extends AbstractController
{
    /**
     * @var RegistrationFormType
     */
    private $registrationFormType;

    /**
     */
    public function __construct()
    {
        $this->registrationFormType = new RegistrationFormType();
    }


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

    private function createRegisterForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->registrationFormType->buildForm(
          $formBuilder,
          []
        );

        return $formBuilder->getForm();
    }
}
