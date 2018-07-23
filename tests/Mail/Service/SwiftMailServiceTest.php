<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Service;

use PHPUnit\Framework\MockObject\MockObject;
use Swift_Plugins_MessageLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Kernel;

class SwiftMailServiceTest extends KernelTestCase
{
    /**
     * @var Swift_Plugins_MessageLogger
     */
    private $messageLogger;

    /**
     * @var UrlGeneratorInterface|MockObject
     */
    private $urlGenerator;

    /**
     * @var SwiftMailService
     */
    private $swiftMailService;

    protected function setUp(): void
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        /** @var \Swift_Mailer $swiftMailer */
        $swiftMailer = $kernel->getContainer()->get('mailer');
        $this->messageLogger = new Swift_Plugins_MessageLogger();
        $swiftMailer->registerPlugin($this->messageLogger);

        /** @var \Twig_Environment $twig */
        $twig = $kernel->getContainer()->get('twig');

        /** @var TranslatorInterface $translator */
        $translator = $kernel->getContainer()->get('translator');

        /** @var UrlGeneratorInterface|MockObject $urlGenerator */
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->urlGenerator = $urlGenerator;

        $this->swiftMailService = new SwiftMailService(
            $swiftMailer,
            $twig,
            $translator,
            $this->urlGenerator,
            ModelsFactory::createSender()
        );
    }

    /**
     * @return string
     */
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_send_a_registration_mail()
    {
        $registration = ModelsFactory::createRegistration();
        $url = 'http://www.gvq.be/nl/view/accounts/activate/00f20af9-c2f5-4bfb-9424-5c0c29fbc2e3';

        $this->urlGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->with(
                'accounts_view_activate',
                [
                    '_locale' => $registration->getUser()->getLanguage()->toNative(),
                    'urlSuffix' => $registration->getUrlSuffix()->toNative(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn($url);

        $this->swiftMailService->sendActivationMail($registration);

        $this->assertEquals(1, $this->messageLogger->countMessages());

        /** @var \Swift_Message $message */
        $message = $this->messageLogger->getMessages()[0];

        $this->assertCount(1, $message->getFrom());
        $this->assertEquals(
            'Info GVQ',
            $message->getFrom()['info@gvq.be']
        );

        $this->assertCount(1, $message->getTo());
        $this->assertEquals(
            $registration->getUser()->getFirstName()->toNative().' '.
            $registration->getUser()->getLastName()->toNative(),
            $message->getTo()[$registration->getUser()->getEmail()->toNative()]
        );

        $this->assertEquals(
            'Activatie Grote Verkeersquiz 2018',
            $message->getSubject()
        );

        $this->assertContains(
            'Beste John',
            $message->getBody()
        );

        $this->assertContains(
            $url,
            $message->getBody()
        );

        // @see: https://github.com/swiftmailer/swiftmailer/issues/736
        /*
        $this->assertEquals(
            'text/html',
            $message->getContentType()
        );
        */

        $this->assertEquals(
            1,
            count($message->getChildren())
        );

        $this->assertContains(
            'Beste John,',
            $message->getChildren()[0]->getBody()
        );

        $this->assertContains(
            $url,
            $message->getChildren()[0]->getBody()
        );

        $this->assertEquals(
            'text/plain',
            $message->getChildren()[0]->getContentType()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_send_a_password_request_mail(): void
    {
        $registration = ModelsFactory::createPasswordRequest();
        $url = 'http://www.gvq.be/nl/view/accounts/password/request/00f20af9-c2f5-4bfb-9424-5c0c29fbc2e3';

        $this->urlGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->with(
                'accounts_view_reset_password',
                [
                    '_locale' => $registration->getUser()->getLanguage()->toNative(),
                    'urlSuffix' => $registration->getUrlSuffix()->toNative(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn($url);

        $this->swiftMailService->sendPasswordRequestMail($registration);

        $this->assertEquals(1, $this->messageLogger->countMessages());

        /** @var \Swift_Message $message */
        $message = $this->messageLogger->getMessages()[0];

        $this->assertCount(1, $message->getFrom());
        $this->assertEquals(
            'Info GVQ',
            $message->getFrom()['info@gvq.be']
        );

        $this->assertEquals(
            $registration->getUser()->getFirstName()->toNative().' '.
            $registration->getUser()->getLastName()->toNative(),
            $message->getTo()[$registration->getUser()->getEmail()->toNative()]
        );

        $this->assertEquals(
            'Aanvraag tot wachtwoord herstel',
            $message->getSubject()
        );

        $this->assertContains(
            'Beste John',
            $message->getBody()
        );

        $this->assertContains(
            $url,
            $message->getBody()
        );

        // @see: https://github.com/swiftmailer/swiftmailer/issues/736
        /*
        $this->assertEquals(
            'text/html',
            $message->getContentType()
        );
        */

        $this->assertEquals(
            1,
            count($message->getChildren())
        );

        $this->assertContains(
            'Beste John,',
            $message->getChildren()[0]->getBody()
        );

        $this->assertContains(
            $url,
            $message->getChildren()[0]->getBody()
        );

        $this->assertEquals(
            'text/plain',
            $message->getChildren()[0]->getContentType()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_send_a_welcome_mail()
    {
        $registration = ModelsFactory::createRegistration();
        $url = 'http://www.gvq.be/nl/view/accounts/login';

        $this->urlGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->with(
                'accounts_view_login',
                [
                    '_locale' => $registration->getUser()->getLanguage()->toNative(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn($url);

        $this->swiftMailService->sendWelcomeMail($registration);

        $this->assertEquals(1, $this->messageLogger->countMessages());

        /** @var \Swift_Message $message */
        $message = $this->messageLogger->getMessages()[0];

        $this->assertCount(1, $message->getFrom());
        $this->assertEquals(
            'Info GVQ',
            $message->getFrom()['info@gvq.be']
        );

        $this->assertCount(1, $message->getTo());
        $this->assertEquals(
            $registration->getUser()->getFirstName()->toNative().' '.
            $registration->getUser()->getLastName()->toNative(),
            $message->getTo()[$registration->getUser()->getEmail()->toNative()]
        );

        $this->assertEquals(
            'Welkom op de Grote Verkeersquiz 2018',
            $message->getSubject()
        );

        $this->assertContains(
            'Beste John',
            $message->getBody()
        );

        $this->assertContains(
            $url,
            $message->getBody()
        );

        // @see: https://github.com/swiftmailer/swiftmailer/issues/736
        /*
        $this->assertEquals(
            'text/html',
            $message->getContentType()
        );
        */

        $this->assertEquals(
            1,
            count($message->getChildren())
        );

        $this->assertContains(
            'Beste John,',
            $message->getChildren()[0]->getBody()
        );

        $this->assertContains(
            $url,
            $message->getChildren()[0]->getBody()
        );

        $this->assertEquals(
            'text/plain',
            $message->getChildren()[0]->getContentType()
        );
    }
}
