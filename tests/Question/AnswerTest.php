<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AnswerTest extends TestCase
{
    /**
     * @var Answer
     */
    private $answer;

    protected function setUp()
    {
        $this->answer = new Answer(
            Uuid::fromString('b7322f69-98cf-4ec4-a551-5d6661fffc17'),
            new NotEmptyString('This is the first answer.')
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id()
    {
        $this->assertEquals(
            Uuid::fromString('b7322f69-98cf-4ec4-a551-5d6661fffc17'),
            $this->answer->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_text()
    {
        $this->assertEquals(
            new NotEmptyString('This is the first answer.'),
            $this->answer->getText()
        );
    }
}
