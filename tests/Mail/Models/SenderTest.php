<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\ValueObjects\Email;

class SenderTest extends TestCase
{
    /**
     * @var Sender
     */
    private $sender;

    protected function setUp(): void
    {
        $this->sender = new Sender(
            new Email('info@gvq.be'),
            new NotEmptyString('Info GVQ')
        );
    }

    /**
     * @test
     */
    public function it_stores_an_email()
    {
        $this->assertEquals(
            new Email('info@gvq.be'),
            $this->sender->getEmail()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_name()
    {
        $this->assertEquals(
            new NotEmptyString('Info GVQ'),
            $this->sender->getName()
        );
    }
}
