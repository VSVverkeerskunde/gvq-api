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
        $subjectId = 'Activation.mail.subject';
        $templateName = 'activate';
        $templateParameters = $this->generateAccountChangeTemplateParameters(
            $registration,
            'accounts_view_activate'
        );

        $message = $this->generateMessage($registration, $subjectId, $templateName, $templateParameters);

        $this->swiftMailer->send($message);
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error
     */
    public function sendPasswordRequestMail(Registration $registration): void
    {
        $subjectId = 'Password.reset.mail.subject';
        $templateName = 'request_password';
        $templateParameters = $this->generateAccountChangeTemplateParameters(
            $registration,
            'accounts_view_reset_password'
        );

        $message = $this->generateMessage($registration, $subjectId, $templateName, $templateParameters);

        $this->swiftMailer->send($message);
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error
     */
    public function sendWelcomeMail(Registration $registration): void
    {
        $subjectId = 'Welcome.mail.subject';
        $templateName = 'welcome';
        $templateParameters = $this->generateWelcomeTemplateParameters($registration);

        $message = $this->generateMessage($registration, $subjectId, $templateName, $templateParameters);

        $this->swiftMailer->send($message);
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error
     */
    public function sendKickOffMail(Registration $registration): void
    {
        $subjectId = 'Kickoff.mail.subject';
        $templateName = 'kick_off';
        $templateParameters = $this->generateKickOffTemplateParameters($registration);

        $message = $this->generateMessage($registration, $subjectId, $templateName, $templateParameters);

        $message
            ->attach(
                \Swift_Attachment::fromPath(
                    'documents/dummy-'.$registration->getUser()->getLanguage()->toNative().'.pdf'
                )
            );

        $this->swiftMailer->send($message);
    }

    /**
     * @param Registration $registration
     * @param string $subjectId
     * @param string $templateName
     * @param array $templateParameters
     * @return Swift_Message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function generateMessage(
        Registration $registration,
        string $subjectId,
        string $templateName,
        array $templateParameters
    ): Swift_Message {
        return (new Swift_Message())
            ->setFrom(
                $this->sender->getEmail()->toNative(),
                $this->sender->getName()->toNative()
            )
            ->setTo(
                $registration->getUser()->getEmail()->toNative(),
                $registration->getUser()->getFirstName()->toNative().' '.
                $registration->getUser()->getLastName()->toNative()
            )
            ->setSubject(
                $this->generateSubject($registration, $subjectId)
            )
            ->setBody(
                $this->twig->render(
                    $this->getHtmlTemplate(
                        $registration->getUser()->getLanguage(),
                        $templateName
                    ),
                    $templateParameters
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    $this->getTextTemplate(
                        $registration->getUser()->getLanguage(),
                        $templateName
                    ),
                    $templateParameters
                ),
                'text/plain'
            );
    }

    /**
     * @param Registration $registration
     * @param string $subjectId
     * @return string
     */
    private function generateSubject(Registration $registration, string $subjectId): string
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
            'activationUrl' => $this->generateUrlWithSuffix($registration, $routeName),
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
     * @return array
     */
    private function generateKickOffTemplateParameters(Registration $registration): array
    {
        return [
            'registration' => $registration,
            'loginUrl' => $this->generateLoginUrl($registration),
            'documentUrl' => $this->generateDocumentUrl(
                'dummy-'.$registration->getUser()->getLanguage()->toNative().'.pdf'
            ),
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

    /**
     * @param string $documentName
     * @return string
     */
    private function generateDocumentUrl(string $documentName): string
    {
        return $this->urlGenerator->generate(
            'documents_kickoff',
            [
                'document' => $documentName,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
