<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;
use VSV\GVQ_API\Account\Forms\RegistrationFormType;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Repositories\UserRepository;

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
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @param TranslatorInterface $translator
     * @param UuidFactoryInterface $uuidFactory
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        TranslatorInterface $translator,
        UuidFactoryInterface $uuidFactory,
        UserRepository $userRepository,
        CompanyRepository $companyRepository
    ) {
        $this->tranlator = $translator;
        $this->uuidFactory = $uuidFactory;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
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

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $this->registrationFormType->createUserFromData($this->uuidFactory, $data);
            $this->userRepository->save($user);
            $company = $this->registrationFormType->createCompanyFromData($this->uuidFactory, $data, $user);
            $this->companyRepository->save($company);
        }

        return $this->render(
            'accounts/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return FormInterface
     */
    private function createRegisterForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder(
            null,
            [
                'validation_groups' => new GroupSequence(['First', 'Default']),
            ]
        );

        $this->registrationFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->tranlator,
            ]
        );

        return $formBuilder->getForm();
    }
}
