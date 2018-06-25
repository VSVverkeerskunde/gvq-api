<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;
use VSV\GVQ_API\Account\Forms\RequestPasswordResetFormType;
use VSV\GVQ_API\Account\Forms\RegistrationFormType;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Registration\Repositories\RegistrationRepository;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffixGenerator;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\Mail\Service\MailService;
use VSV\GVQ_API\User\ValueObjects\Email;

class AccountViewController extends AbstractController
{
    /**
     * @var RegistrationFormType
     */
    private $registrationFormType;

    /**
     * @var RequestPasswordResetFormType
     */
    private $requestPasswordResetFormType;

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
        $this->requestPasswordResetFormType = new RequestPasswordResetFormType();
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

            try {
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

                $registration = $this->registrationFormType->createRegistrationForUser(
                    $this->uuidFactory,
                    $this->urlSuffixGenerator,
                    $user,
                    false
                );
                $this->registrationRepository->save($registration);

                return $this->redirectToRoute('accounts_view_register_success');
            } catch (\Exception $e) {
                $this->addFlash('danger', $this->translator->trans('Registration error'));
            }
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
    public function requestPasswordReset(Request $request): Response
    {
        $form = $this->createRequestPasswordResetForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $this->userRepository->getByEmail(new Email($data['email']));

            if ($user && $user->isActive()) {
                $existingRegistration = $this->registrationRepository->getByUserId($user->getId());
                if ($existingRegistration) {
                    $this->registrationRepository->delete($existingRegistration->getId());
                }
                $registration = $this->registrationFormType->createRegistrationForUser(
                    $this->uuidFactory,
                    $this->urlSuffixGenerator,
                    $user,
                    true
                );

                $this->registrationRepository->save($registration);
                $this->mailService->sendPasswordResetMail($registration);
            }

            return $this->redirectToRoute('accounts_view_request_password_reset_success');
        }

        return $this->render(
            'accounts/request_password_reset.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return Response
     */
    public function requestPasswordResetSuccess(): Response
    {
        return $this->render('accounts/request_password_reset_success.html.twig');
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
    private function createRequestPasswordResetForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->requestPasswordResetFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }
}
