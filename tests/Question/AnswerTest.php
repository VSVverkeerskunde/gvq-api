<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class AnswerTest extends TestCase
{
    /**
     * @var Answer
     */
    private $answer;

    protected function setUp()
    {
        $text = new NotEmptyString('This is the first answer.');
        $this->answer = new Answer(
            1,
            $text
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id()
    {
        $this->assertEquals(
            1,
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
