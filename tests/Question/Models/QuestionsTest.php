<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class QuestionsTest extends TestCase
{
    /**
     * @var Question[]
     */
    private $questionsArray;

    /**
     * @var Questions
     */
    private $questions;

    protected function setUp(): void
    {
        $this->questionsArray = [
            ModelsFactory::createAccidentQuestion(),
            ModelsFactory::createGeneralQuestion(),
        ];

        $this->questions = new Questions(...$this->questionsArray);
    }

    /**
     * @test
     */
    public function it_can_iterate(): void
    {
        $actualQuestions = [];
        foreach ($this->questions as $question) {
            $actualQuestions[] = $question;
        }

        $this->assertEquals($this->questionsArray, $actualQuestions);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertInstanceOf(\Countable::class, $this->questions);
        $this->assertEquals(2, count($this->questions));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->questionsArray,
            $this->questions->toArray()
        );
    }
}
