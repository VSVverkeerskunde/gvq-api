<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class AnswersTest extends TestCase
{
    /**
     * @var Answer[]
     */
    private $answersArray;

    /**
     * @var Answers
     */
    private $answers;

    protected function setUp(): void
    {
        $this->answersArray = [
            new Answer(
                Uuid::fromString('b1a4a8a4-6419-449f-bde2-10122d90a916'),
                new NotEmptyString('text'),
                false
            ),
            new Answer(
                Uuid::fromString('bfc153e0-8fea-489b-9010-1dfe9f9dbba8'),
                new NotEmptyString('text'),
                false
            ),
        ];

        $this->answers = new Answers(...$this->answersArray);
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

        $this->assertEquals($this->answersArray, $actualArray);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(2, count($this->answers));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->answersArray,
            $this->answers->toArray()
        );
    }
}
