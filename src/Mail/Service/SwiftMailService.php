<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Service;

use \Swift_Mailer;
use \Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use \Twig_Environment;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Mail\Models\Sender;
use VSV\GVQ_API\Registration\Models\Registration;

class SwiftMailService implements MailService
{
    /**
     * @var Swift_Mailer
     */
    private $swiftMailer;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @param Swift_Mailer $swiftMailer
     * @param Twig_Environment $twig
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param Sender $sender
     */
    public function __construct(
        Swift_Mailer $swiftMailer,
        Twig_Environment $twig,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        Sender $sender
    ) {
        $this->swiftMailer = $swiftMailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->sender = $sender;
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error
     */
    public function sendActivationMail(Registration $registration): void
    {
        $message = $this->createMessage($registration);
        $message
            ->setSubject(
                $this->createSubject($registration, 'Activation.mail.subject')
            )
            ->setBody(
                $this->twig->render(
                    $this->getHtmlTemplate(
                        $registration->getUser()->getLanguage(),
                        'activate'
                    ),
                    $this->generateAccountChangeTemplateParameters($registration, 'accounts_view_activate')
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    $this->getTextTemplate(
                        $registration->getUser()->getLanguage(),
                        'activate'
                    ),
                    $this->generateAccountChangeTemplateParameters($registration, 'accounts_view_activate')
                ),
                'text/plain'
            );

        $this->swiftMailer->send($message);
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error
     */
    public function sendPasswordRequestMail(Registration $registration): void
    {
        $message = $this->createMessage($registration);
        $message
            ->setSubject(
                $this->createSubject($registration, 'Password.reset.mail.subject')
            )
            ->setBody(
                $this->twig->render(
                    $this->getHtmlTemplate(
                        $registration->getUser()->getLanguage(),
                        'request_password'
                    ),
                    $this->generateAccountChangeTemplateParameters($registration, 'accounts_view_reset_password')
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    $this->getTextTemplate(
                        $registration->getUser()->getLanguage(),
                        'request_password'
                    ),
                    $this->generateAccountChangeTemplateParameters($registration, 'accounts_view_reset_password')
                ),
                'text/plain'
            );

        $this->swiftMailer->send($message);
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error
     */
    public function sendWelcomeMail(Registration $registration): void
    {
        $message = $this->createMessage($registration);

        $message
            ->setSubject(
                $this->createSubject($registration, 'Welcome.mail.subject')
            )
            ->setBody(
                $this->twig->render(
                    $this->getHtmlTemplate(
                        $registration->getUser()->getLanguage(),
                        'welcome'
                    ),
                    $this->generateWelcomeTemplateParameters($registration)
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    $this->getTextTemplate(
                        $registration->getUser()->getLanguage(),
                        'welcome'
                    ),
                    $this->generateWelcomeTemplateParameters($registration)
                ),
                'text/plain'
            );

        $this->swiftMailer->send($message);
    }

    /**
     * @param Registration $registration
     * @return Swift_Message
     */
    private function createMessage(Registration $registration): Swift_Message
    {
        $message = (new Swift_Message())
            ->setFrom(
                $this->sender->getEmail()->toNative(),
                $this->sender->getName()->toNative()
            )
            ->setTo(
                $registration->getUser()->getEmail()->toNative(),
                $registration->getUser()->getFirstName()->toNative().' '.
                $registration->getUser()->getLastName()->toNative()
            );

        return $message;
    }

    /**
     * @param Registration $registration
     * @param string $subjectId
     * @return string
     */
    private function createSubject(Registration $registration, string $subjectId): string
    {
        return $this->translator->trans(
            $subjectId,
            [],
            null,
            $registration->getUser()->getLanguage()->toNative()
        );
    }

    /**
     * @param Language $language
     * @param string $templateName
     * @return string
     */
    private function getHtmlTemplate(Language $language, string $templateName): string
    {
        return 'mails/'.$templateName.'.'.$language->toNative().'.html.twig';
    }

    /**
     * @param Language $language
     * @param string $templateName
     * @return string
     */
    private function getTextTemplate(Language $language, string $templateName): string
    {
        return 'mails/'.$templateName.'.'.$language->toNative().'.text.twig';
    }

    /**
     * @param Registration $registration
     * @param string $routeName
     * @return array
     */
    private function generateAccountChangeTemplateParameters(Registration $registration, string $routeName): array
    {
        return [
            'registration' => $registration,
            'activationUrl' => $this->generateUrlWithSuffix($registration, 'accounts_view_activate'),
        ];
    }

    /**
     * @param Registration $registration
     * @return array
     */
    private function generateWelcomeTemplateParameters(Registration $registration): array
    {
        return [
            'registration' => $registration,
            'loginUrl' => $this->generateLoginUrl($registration),
        ];
    }

    /**
     * @param Registration $registration
     * @param string $routeName
     * @return string
     */
    private function generateUrlWithSuffix(Registration $registration, string $routeName): string
    {
        return $this->urlGenerator->generate(
            $routeName,
            [
                '_locale' => $registration->getUser()->getLanguage()->toNative(),
                'urlSuffix' => $registration->getUrlSuffix()->toNative(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param Registration $registration
     * @return string
     */
    private function generateLoginUrl(Registration $registration): string
    {
        return $this->urlGenerator->generate(
            'accounts_view_login',
            [
                '_locale' => $registration->getUser()->getLanguage()->toNative(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
