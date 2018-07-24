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

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->questionsArray = [
            ModelsFactory::createAccidentQuestionWithCreatedOn(
                new \DateTimeImmutable('2020-02-02T00:00:00+00:00')
            ),
            ModelsFactory::createAccidentQuestionWithCreatedOn(
                new \DateTimeImmutable('2020-02-01T23:00:00+00:00')
            ),
            ModelsFactory::createAccidentQuestionWithCreatedOn(
                new \DateTimeImmutable('2020-02-02T01:00:00+00:00')
            ),
            ModelsFactory::createAccidentQuestionWithCreatedOn(
                new \DateTimeImmutable('2020-02-02T00:00:00+00:00')
            ),
        ];

        $this->questions = new Questions(...$this->questionsArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_questions(): void
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
        $this->assertEquals(4, count($this->questions));
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

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_sort_by_created_on(): void
    {
        $this->assertEquals(
            $this->questions->toArray()[0]->getCreatedOn(),
            new \DateTimeImmutable('2020-02-02T00:00:00+00:00')
        );

        $this->questions->sortByNewest();

        $this->assertEquals(
            $this->questions->toArray()[0]->getCreatedOn(),
            new \DateTimeImmutable('2020-02-02T01:00:00+00:00')
        );

        $this->assertEquals(
            $this->questions->toArray()[1]->getCreatedOn(),
            new \DateTimeImmutable('2020-02-02T00:00:00+00:00')
        );

        $this->assertEquals(
            $this->questions->toArray()[2]->getCreatedOn(),
            new \DateTimeImmutable('2020-02-02T00:00:00+00:00')
        );

        $this->assertEquals(
            $this->questions->toArray()[3]->getCreatedOn(),
            new \DateTimeImmutable('2020-02-01T23:00:00+00:00')
        );
    }
}
