<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Controllers;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;
use VSV\GVQ_API\Account\Forms\EditPasswordFormType;
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
use VSV\GVQ_API\User\Repositories\Entities\UserEntity;
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
     * @var EditPasswordFormType
     */
    private $editPasswordFormType;

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
     * @var \DateTimeImmutable
     */
    private $quizStartDate;

    /**
     * @var \DateTimeImmutable
     */
    private $quizKickOffDate;

    /**
     * @var bool
     */
    private $registrationsClosed;

    /**
     * @param TranslatorInterface $translator
     * @param UuidFactoryInterface $uuidFactory
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param RegistrationRepository $registrationRepository
     * @param UrlSuffixGenerator $urlSuffixGenerator
     * @param MailService $mailService
     * @param \DateTimeImmutable $quizStartDate
     * @param \DateTimeImmutable $quizKickOffDate
     * @param bool $registrationsClosed
     */
    public function __construct(
        TranslatorInterface $translator,
        UuidFactoryInterface $uuidFactory,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        RegistrationRepository $registrationRepository,
        UrlSuffixGenerator $urlSuffixGenerator,
        MailService $mailService,
        \DateTimeImmutable $quizStartDate,
        \DateTimeImmutable $quizKickOffDate,
        bool $registrationsClosed = false
    ) {
        $this->translator = $translator;
        $this->uuidFactory = $uuidFactory;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->urlSuffixGenerator = $urlSuffixGenerator;
        $this->registrationRepository = $registrationRepository;
        $this->mailService = $mailService;
        $this->quizStartDate = $quizStartDate;
        $this->quizKickOffDate = $quizKickOffDate;
        $this->registrationsClosed = $registrationsClosed;

        $this->registrationFormType = new RegistrationFormType();
        $this->requestPasswordFormType = new RequestPasswordFormType();
        $this->resetPasswordFormType = new ResetPasswordFormType();
        $this->loginFormType = new LoginFormType();
        $this->editPasswordFormType = new EditPasswordFormType();
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->redirectToLandingPage();
    }

    private function canRegister(): bool
    {
        return
            !$this->registrationsClosed ||
            $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_VSV');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request): Response
    {
        if (!$this->canRegister()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $form = $this->createRegisterForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->honeypotTricked($data)) {
                return $this->handleHoneypotField('accounts_view_register');
            }

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
    public function info(): Response
    {
        return $this->render(
            'accounts/info.html.twig',
            [
                'registrations_closed' => $this->registrationsClosed,
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

            if ($this->honeypotTricked($data)) {
                return $this->handleHoneypotField('accounts_view_request_password');
            }

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

            if ($this->honeypotTricked($data)) {
                return $this->handleHoneypotField('accounts_view_login');
            }

            $user = $this->userRepository->getByEmail(new Email($data['email']));

            if ($user && $user->getPassword() && $user->getPassword()->verifies($data['password'])) {
                if ($user->isActive()) {
                    $securityUser = UserEntity::fromUser($user);
                    $token = new UsernamePasswordToken(
                        $securityUser,
                        null,
                        'main',
                        $securityUser->getRoles()
                    );
                    $this->get('security.token_storage')->setToken($token);
                    $this->get('session')->set('_security_main', serialize($token));

                    return $this->redirectToLandingPage();
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
     * @throws \Exception
     */
    public function activate(string $urlSuffix): Response
    {
        $registration = $this->registrationRepository->getByUrlSuffix(
            new UrlSuffix($urlSuffix)
        );

        if ($registration && $registration->isUsed()) {
            return $this->render('accounts/activate_already_used.html.twig');
        }
        elseif ($registration) {
            $user = $registration->getUser()->activate();
            $this->userRepository->update($user);

            $registration->setUsed();
            $this->registrationRepository->save($registration);

            $this->mailService->sendWelcomeMail($registration);

            $now = new \DateTimeImmutable();

            if ($now >= $this->quizStartDate) {
                $this->mailService->sendKickOffMailAfterLaunch($registration);
            } elseif ($now >= $this->quizKickOffDate) {
                $this->mailService->sendKickOffMail($registration);
            }

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
     * @param Request $request
     * @return Response
     */
    public function editPassword(Request $request): Response
    {
        $form = $this->createEditPasswordForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $this->userRepository->getByEmail(new Email($this->getUser()->getUsername()));

            if ($user) {
                $user = $this->editPasswordFormType->editUserPassword($user, $data);
                $this->userRepository->updatePassword($user);
            }

            return $this->redirectToRoute('accounts_logout');
        }

        return $this->render(
            'accounts/edit_password.html.twig',
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

    private function createEditPasswordForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->editPasswordFormType->buildForm(
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

    /**
     * @param array $data
     * @return bool
     */
    private function honeypotTricked(array $data)
    {
        return !empty($data['azijnpotje']);
    }

    /**
     * @param string $route
     * @return Response
     */
    private function handleHoneypotField(string $route): Response
    {
        return $this->redirectToRoute($route);
    }

    /**
     * @return RedirectResponse
     */
    private function redirectToLandingPage(): RedirectResponse
    {
        if ($this->get('security.authorization_checker')->isGranted(['ROLE_VSV', 'ROLE_ADMIN'])) {
            return $this->redirectToRoute('questions_view_index');
        } else {
            return $this->redirectToRoute('dashboard');
        }
    }
}
