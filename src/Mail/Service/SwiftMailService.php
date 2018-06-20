<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Service;

use \Swift_Mailer;
use \Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use \Twig_Environment;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Mail\Models\Sender;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\User\ValueObjects\Email;

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
     */
    public function __construct(
        Swift_Mailer $swiftMailer,
        Twig_Environment $twig,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->swiftMailer = $swiftMailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;

        // TODO: Inject the Sender.
        $this->sender = new Sender(
            new Email('info@gvq.be'),
            new NotEmptyString('Ingo GVQ')
        );
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error
     */
    public function sendPasswordResetMail(Registration $registration): void
    {
        $message = (new Swift_Message())
            ->setFrom(
                $this->sender->getEmail()->toNative(),
                $this->sender->getName()->toNative()
            )
            ->setTo(
                $registration->getUser()->getEmail()->toNative(),
                $registration->getUser()->getLastName()->toNative()
            )
            ->setSubject(
                $this->translator->trans(
                    'Password-reset.mail.subject',
                    [],
                    null,
                    $registration->getUser()->getLanguage()->toNative()
                )
            )
            ->setBody(
                $this->twig->render(
                    $this->getPasswordResetHtmlTemplate(
                        $registration->getUser()->getLanguage()
                    ),
                    $this->generateTemplateParameters($registration)
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    $this->getPasswordResetTextTemplate(
                        $registration->getUser()->getLanguage()
                    ),
                    $this->generateTemplateParameters($registration)
                ),
                'text/plain'
            );

        $this->swiftMailer->send($message);
    }

    /**
     * @param Language $language
     * @return string
     */
    private function getPasswordResetHtmlTemplate(Language $language): string
    {
        return 'mails/password_reset.'.$language->toNative().'.html.twig';
    }

    /**
     * @param Language $language
     * @return string
     */
    private function getPasswordResetTextTemplate(Language $language): string
    {
        return 'mails/password_reset.'.$language->toNative().'.text.twig';
    }

    /**
     * @param Registration $registration
     * @return array
     */
    private function generateTemplateParameters(Registration $registration): array
    {
        return [
            'registration' => $registration,
            'activationUrl' => $this->generatePasswordResetUrl($registration),
        ];
    }

    /**
     * @param Registration $registration
     * @return string
     */
    private function generatePasswordResetUrl(Registration $registration): string
    {
        return $this->urlGenerator->generate(
            'accounts_view_password_reset',
            [
                '_locale' => $registration->getUser()->getLanguage()->toNative(),
                'id' => $registration->getUrlSuffix()->toNative(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
