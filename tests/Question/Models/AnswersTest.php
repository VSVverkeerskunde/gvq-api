<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;

class AnswersTest extends TestCase
{
    /**
     * @var Answer[]
     */
    private $answersArray;

    /**
     * @var Answer[]
     */
    private $sortedAnswersArray;

    /**
     * @var Answers
     */
    private $answers;

    protected function setUp(): void
    {
        $this->answersArray = [
            new Answer(
                Uuid::fromString('bfc153e0-8fea-489b-9010-1dfe9f9dbba8'),
                new PositiveNumber(2),
                new NotEmptyString('answer 2'),
                false
            ),
            new Answer(
                Uuid::fromString('b1a4a8a4-6419-449f-bde2-10122d90a916'),
                new PositiveNumber(1),
                new NotEmptyString('answer 1'),
                false
            ),
            new Answer(
                Uuid::fromString('4caf0217-ca8d-46ca-8151-151d71420910'),
                new PositiveNumber(3),
                new NotEmptyString('answer 3'),
                true
            ),
        ];

        $this->sortedAnswersArray = $this->answersArray;
        usort(
            $this->sortedAnswersArray,
            function (Answer $a1, Answer $a2) {
                return $a1->getIndex()->toNative() - $a2->getIndex()->toNative();
            }
        );

        $this->answers = new Answers(...$this->answersArray);
    }

    /**
     * @test
     */
    public function it_sorts_on_answer_index(): void
    {
        for ($index = 0; $index < count($this->answers); $index++) {
            $this->assertEquals(
                $index + 1,
                $this->answers->toArray()[$index]->getIndex()->toNative()
            );
        }
    }

    /**
     * @test
     */
    public function it_can_iterate_over_answers(): void
    {
        $actualArray = [];
        foreach ($this->answers as $answer) {
            $actualArray[] = $answer;
        }

        $this->assertEquals($this->sortedAnswersArray, $actualArray);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(3, count($this->answers));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->sortedAnswersArray,
            $this->answers->toArray()
        );
    }
}
