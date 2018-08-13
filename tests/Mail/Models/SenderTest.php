<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\ValueObjects\Email;

class SenderTest extends TestCase
{
    /**
     * @var Sender
     */
    private $sender;

    protected function setUp(): void
    {
        $this->sender = ModelsFactory::createSenderNl();
    }

    /**
     * @test
     */
    public function it_stores_an_email()
    {
        $this->assertEquals(
            new Email('quiz@vsv.be'),
            $this->sender->getEmail()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_name()
    {
        $this->assertEquals(
            new NotEmptyString('Grote verkeersquiz 2018'),
            $this->sender->getName()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_language()
    {
        $this->assertEquals(
            new Language('nl'),
            $this->sender->getLanguage()
        );
    }
}
