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
        $message = (new Swift_Message())
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
                $this->translator->trans(
                    'Activation.mail.subject',
                    [],
                    null,
                    $registration->getUser()->getLanguage()->toNative()
                )
            )
            ->setBody(
                $this->twig->render(
                    $this->getActivationHtmlTemplate(
                        $registration->getUser()->getLanguage()
                    ),
                    $this->generateActivationTemplateParameters($registration)
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    $this->getActivationTextTemplate(
                        $registration->getUser()->getLanguage()
                    ),
                    $this->generateActivationTemplateParameters($registration)
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
        $message = (new Swift_Message())
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
                $this->translator->trans(
                    'Password.reset.mail.subject',
                    [],
                    null,
                    $registration->getUser()->getLanguage()->toNative()
                )
            )
            ->setBody(
                $this->twig->render(
                    $this->getPasswordRequestHtmlTemplate(
                        $registration->getUser()->getLanguage()
                    ),
                    $this->generatePasswordRequestTemplateParameters($registration)
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    $this->getPasswordRequestTextTemplate(
                        $registration->getUser()->getLanguage()
                    ),
                    $this->generatePasswordRequestTemplateParameters($registration)
                ),
                'text/plain'
            );

        $this->swiftMailer->send($message);
    }

    /**
     * @param Language $language
     * @return string
     */
    private function getActivationHtmlTemplate(Language $language): string
    {
        return 'mails/activate.'.$language->toNative().'.html.twig';
    }

    /**
     * @param Language $language
     * @return string
     */
    private function getActivationTextTemplate(Language $language): string
    {
        return 'mails/activate.'.$language->toNative().'.text.twig';
    }

    /**
     * @param Language $language
     * @return string
     */
    private function getPasswordRequestHtmlTemplate(Language $language): string
    {
        return 'mails/request_password.'.$language->toNative().'.html.twig';
    }

    /**
     * @param Language $language
     * @return string
     */
    private function getPasswordRequestTextTemplate(Language $language): string
    {
        return 'mails/request_password.'.$language->toNative().'.text.twig';
    }

    /**
     * @param Registration $registration
     * @return array
     */
    private function generateActivationTemplateParameters(Registration $registration): array
    {
        return [
            'registration' => $registration,
            'activationUrl' => $this->generateActivationUrl($registration),
        ];
    }

    /**
     * @param Registration $registration
     * @return array
     */
    private function generatePasswordRequestTemplateParameters(Registration $registration): array
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
    private function generateActivationUrl(Registration $registration): string
    {
        return $this->urlGenerator->generate(
            'accounts_view_activate',
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
    private function generatePasswordResetUrl(Registration $registration): string
    {
        return $this->urlGenerator->generate(
            'accounts_view_reset_password',
            [
                '_locale' => $registration->getUser()->getLanguage()->toNative(),
                'urlSuffix' => $registration->getUrlSuffix()->toNative(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
