<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Service;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Registration\Models\Registration;

class MailTestHelper
{
    /**
     * @var TestCase
     */
    private $testCase;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * @param \Swift_Message $message
     * @param Registration $registration
     * @param string $url
     * @param string $subject
     */
    public function testCommonMessageAsserts(
        \Swift_Message $message,
        Registration $registration,
        string $url,
        string $subject
    ): void {
        $this->testCase->assertCount(1, $message->getFrom());
        $this->testCase->assertEquals(
            'Info GVQ',
            $message->getFrom()['info@gvq.be']
        );

        $this->testCase->assertCount(1, $message->getTo());
        $this->testCase->assertEquals(
            $registration->getUser()->getFirstName()->toNative().' '.
            $registration->getUser()->getLastName()->toNative(),
            $message->getTo()[$registration->getUser()->getEmail()->toNative()]
        );

        $this->testCase->assertEquals(
            $subject,
            $message->getSubject()
        );

        $this->testCase->assertContains(
            'Beste John',
            $message->getBody()
        );

        $this->testCase->assertContains(
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

        $this->testCase->assertContains(
            'Beste John,',
            $message->getChildren()[0]->getBody()
        );

        $this->testCase->assertContains(
            $url,
            $message->getChildren()[0]->getBody()
        );

        $this->testCase->assertEquals(
            'text/plain',
            $message->getChildren()[0]->getContentType()
        );
    }
}
