<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Service;

use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;
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
     * @var Sender[]
     */
    private $senders;

    /**
     * @param Swift_Mailer $swiftMailer
     * @param Twig_Environment $twig
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param Sender ...$senders
     */
    public function __construct(
        Swift_Mailer $swiftMailer,
        Twig_Environment $twig,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        Sender ...$senders
    ) {
        $this->swiftMailer = $swiftMailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->senders = $senders;
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
    public function sendKickOffMailAfterLaunch(Registration $registration): void
    {
        $subjectId = 'Kickoff.mail.subject';
        $templateName = 'kick_off_after_launch';
        $templateParameters = $this->generateKickOffTemplateParameters($registration);

        $message = $this->generateMessage($registration, $subjectId, $templateName, $templateParameters);

        // @codeCoverageIgnoreStart
        $documentName = $this->getKickOffDocumentName($registration);
        if ($registration->getUser()->getLanguage()->toNative() === Language::FR) {
            $documentPath = 'documents/fr/'.$documentName;
        } else {
            $documentPath = 'documents/nl/'.$documentName;
        }
        // @codeCoverageIgnoreEnd

        $message->attach(Swift_Attachment::fromPath($documentPath));

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

        // @codeCoverageIgnoreStart
        $documentName = $this->getKickOffDocumentName($registration);
        if ($registration->getUser()->getLanguage()->toNative() === Language::FR) {
            $documentPath = 'documents/fr/'.$documentName;
        } else {
            $documentPath = 'documents/nl/'.$documentName;
        }
        // @codeCoverageIgnoreEnd

        $message->attach(Swift_Attachment::fromPath($documentPath));

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
        $sender = $this->getSenderForLanguage($registration);

        return (new Swift_Message())
            ->setFrom(
                $sender->getEmail()->toNative(),
                $sender->getName()->toNative()
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
     * @return Sender
     */
    private function getSenderForLanguage(Registration $registration): Sender
    {
        $senderForLanguage = $this->senders[0];

        foreach ($this->senders as $sender) {
            if ($registration->getUser()->getLanguage()->equals($sender->getLanguage())) {
                $senderForLanguage = $sender;
            }
        }

        return $senderForLanguage;
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
            'documentsUrl' => $this->generateDocumentsUrl($registration),
            'documentUrl' => $this->generateKickOffDocumentUrl($registration),
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
     * @param Registration $registration
     * @return string
     */
    private function getKickOffDocumentName(Registration $registration): string
    {
        // @codeCoverageIgnoreStart
        if ($registration->getUser()->getLanguage()->toNative() === Language::FR) {
            return 'Briefing_entreprise_2019.pdf';
        } else {
            return 'Briefing_bedrijven_2019.pdf';
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param Registration $registration
     * @return string
     */
    private function generateDocumentsUrl(Registration $registration): string
    {
        return $this->urlGenerator->generate(
            'documents',
            [
                '_locale' => $registration->getUser()->getLanguage()->toNative(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param Registration $registration
     * @return string
     */
    private function generateKickOffDocumentUrl(Registration $registration): string
    {
        $documentName = $this->getKickOffDocumentName($registration);

        return $this->urlGenerator->generate(
            'documents_kickoff',
            [
                '_locale' => $registration->getUser()->getLanguage()->toNative(),
                'document' => $documentName,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
