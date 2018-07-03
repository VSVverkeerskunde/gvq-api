<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Controllers;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;
use VSV\GVQ_API\Account\Forms\LoginFormType;
use VSV\GVQ_API\Account\Forms\RegistrationFormType;
use VSV\GVQ_API\Account\Forms\RequestPasswordFormType;
use VSV\GVQ_API\Account\Forms\ResetPasswordFormType;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Mail\Service\MailService;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\Repositories\RegistrationRepository;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffixGenerator;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;

class AccountViewController extends AbstractController
{
    /**
     * @var RegistrationFormType
     */
    private $registrationFormType;

    /**
     * @var RequestPasswordFormType
     */
    private $requestPasswordFormType;

    /**
     * @var ResetPasswordFormType
     */
    private $resetPasswordFormType;

    /**
     * @var LoginFormType
     */
    private $loginFormType;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var RegistrationRepository
     */
    private $registrationRepository;

    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var UrlSuffixGenerator
     */
    private $urlSuffixGenerator;

    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @param TranslatorInterface $translator
     * @param UuidFactoryInterface $uuidFactory
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param RegistrationRepository $registrationRepository
     * @param UrlSuffixGenerator $urlSuffixGenerator
     * @param MailService $mailService
     */
    public function __construct(
        TranslatorInterface $translator,
        UuidFactoryInterface $uuidFactory,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        RegistrationRepository $registrationRepository,
        UrlSuffixGenerator $urlSuffixGenerator,
        MailService $mailService
    ) {
        $this->translator = $translator;
        $this->uuidFactory = $uuidFactory;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->urlSuffixGenerator = $urlSuffixGenerator;
        $this->registrationRepository = $registrationRepository;
        $this->mailService = $mailService;

        $this->registrationFormType = new RegistrationFormType();
        $this->requestPasswordFormType = new RequestPasswordFormType();
        $this->resetPasswordFormType = new ResetPasswordFormType();
        $this->loginFormType = new LoginFormType();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request): Response
    {
        $form = $this->createRegisterForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $language = $request->getLocale();

            $user = $this->registrationFormType->createUserFromData(
                $this->uuidFactory,
                $data,
                $language
            );
            $this->userRepository->save($user);

            $company = $this->registrationFormType->createCompanyFromData(
                $this->uuidFactory,
                $data,
                $user
            );
            $this->companyRepository->save($company);

            $registration = $this->createRegistrationForUser(
                $user,
                false
            );
            $this->registrationRepository->save($registration);

            $this->mailService->sendActivationMail($registration);

            return $this->redirectToRoute('accounts_view_register_success');
        }

        return $this->render(
            'accounts/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return Response
     */
    public function registerSuccess(): Response
    {
        return $this->render('accounts/register_success.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function requestPassword(Request $request): Response
    {
        $form = $this->createRequestPasswordForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $this->userRepository->getByEmail(new Email($data['email']));

            if ($user && $user->isActive()) {
                $existingRegistration = $this->registrationRepository->getByUserId($user->getId());
                if ($existingRegistration) {
                    $this->registrationRepository->delete($existingRegistration->getId());
                }
                $registration = $this->createRegistrationForUser(
                    $user,
                    true
                );

                $this->registrationRepository->save($registration);
                $this->mailService->sendPasswordRequestMail($registration);
            }

            if ($user && !$user->isActive()) {
                $this->addFlash('warning', $this->translator->trans('Account.inactive'));
            } else {
                return $this->redirectToRoute('accounts_view_request_password_success');
            }
        }

        return $this->render(
            'accounts/request_password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return Response
     */
    public function requestPasswordSuccess(): Response
    {
        return $this->render('accounts/request_password_success.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function resetPassword(Request $request): Response
    {
        $registration = $this->registrationRepository->getByUrlSuffix(
            new UrlSuffix($request->get('urlSuffix'))
        );
        if (!$registration) {
            return $this->render('accounts/reset_password_link_error.html.twig');
        }

        $form = $this->createResetPasswordForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $registration->getUser();

            if ($user) {
                $user = $user->withPassword(Password::fromPlainText($data['password']));
                $this->userRepository->updatePassword($user);
                $this->registrationRepository->delete($registration->getId());
            }

            return $this->redirectToRoute('accounts_view_reset_password_success');
        }

        return $this->render(
            'accounts/reset_password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return Response
     */
    public function resetPasswordSuccess(): Response
    {
        return $this->render('accounts/reset_password_success.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $form = $this->createLoginForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $this->userRepository->getByEmail(new Email($data['email']));

            if ($user && $user->getPassword() && $user->getPassword()->verifies($data['password'])) {
                if ($user->isActive()) {
                    return $this->redirectToRoute('questions_view_index');
                }
                $this->addFlash('warning', $this->translator->trans('Account.inactive'));
            } else {
                $this->addFlash('danger', $this->translator->trans('Credentials.invalid'));
            }
        }

        return $this->render(
            'accounts/login.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param string $urlSuffix
     * @return Response
     */
    public function activate(string $urlSuffix): Response
    {
        $registration = $this->registrationRepository->getByUrlSuffix(
            new UrlSuffix($urlSuffix)
        );

        if ($registration) {
            $user = $registration->getUser()->activate();
            $this->userRepository->update($user);

            $this->registrationRepository->delete($registration->getId());

            return $this->render('accounts/activate.html.twig');
        } else {
            return $this->render('accounts/activate_error.html.twig');
        }
    }


    /**
     * @param Request $request
     * @param string $id
     * @return Response
     * @throws \Exception
     */
    public function sendActivation(Request $request, string $id): Response
    {
        $user = $this->userRepository->getById(Uuid::fromString($id));

        if (!$user) {
            $this->addFlash(
                'warning',
                $this->translator->trans(
                    'User.id.invalid',
                    [
                        '%id%' => $id,
                    ]
                )
            );

            return $this->redirectToRoute('users_view_index');
        }

        if ($request->getMethod() === 'POST') {
            $existingRegistration = $this->registrationRepository->getByUserId($user->getId());
            if ($existingRegistration) {
                $this->registrationRepository->delete(
                    $existingRegistration->getId()
                );
            }

            $registration = $this->createRegistrationForUser(
                $user,
                false
            );
            $this->registrationRepository->save($registration);

            $this->mailService->sendActivationMail($registration);

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'Activation.send.success',
                    [
                        '%email%' => $user->getEmail()->toNative(),
                    ]
                )
            );

            return $this->redirectToRoute('users_view_index');
        }

        return $this->render(
            'users/send_activation.html.twig',
            [
                'email' => $user->getEmail()->toNative(),
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
                'validation_groups' => new GroupSequence(
                    [
                        'CorrectSyntax',
                        'Default',
                    ]
                ),
            ]
        );

        $this->registrationFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @return FormInterface
     */
    private function createLoginForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->loginFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @return FormInterface
     */
    private function createRequestPasswordForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->requestPasswordFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @return FormInterface
     */
    private function createResetPasswordForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->resetPasswordFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @param User $user
     * @param bool $passwordReset
     * @return Registration
     * @throws \Exception
     */
    private function createRegistrationForUser(
        User $user,
        bool $passwordReset
    ): Registration {
        return new Registration(
            $this->uuidFactory->uuid4(),
            $this->urlSuffixGenerator->createUrlSuffix(),
            $user,
            new \DateTimeImmutable(),
            $passwordReset
        );
    }
}
