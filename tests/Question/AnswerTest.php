<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AnswerTest extends TestCase
{
    /**
     * @var Answer
     */
    private $answer;

    /**
     * @var UuidInterface
     */
    private $uuid;

    protected function setUp()
    {
        $text = new NotEmptyString('This is the first answer.');
        $this->uuid = Uuid::uuid4();
        $this->answer = new Answer(
            $this->uuid,
            $text
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id()
    {
        $this->assertEquals(
            $this->uuid,
            $this->answer->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_text()
    {
        $this->assertEquals(
            'This is the first answer.',
            $this->answer->getText()
        );
    }
}
