<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class AnswersTest extends TestCase
{
    /**
     * @test
     * @dataProvider answersProvider
     * @param Answer ...$answers
     */
    public function it_throws_on_wrong_number_of_arguments(Answer ...$answers)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount of answers must be 2 or 3.');

        new Answers(...$answers);
    }

    /**
     * @return Answer[][]
     */
    public function answersProvider(): array
    {
        $answer1 = new Answer(
            Uuid::fromString('b1a4a8a4-6419-449f-bde2-10122d90a916'),
            new NotEmptyString('text')
        );
        $answer2 = new Answer(
            Uuid::fromString('bfc153e0-8fea-489b-9010-1dfe9f9dbba8'),
            new NotEmptyString('text')
        );
        $answer3 = new Answer(
            Uuid::fromString('822dd8f9-c86b-4531-be92-b35627a21ba4'),
            new NotEmptyString('text')
        );
        $answer4 = new Answer(
            Uuid::fromString('50f0551b-a239-4554-96dc-4f4778e8d63a'),
            new NotEmptyString('text')
        );

        return [
            [
                $answer1,
                $answer2,
                $answer3,
                $answer4,
            ],
            [
                $answer1,
            ],
        ];
    }

    /**
     * @test
     */
    public function it_can_iterate_over_answers()
    {
        $answer1 = new Answer(
            Uuid::fromString('b1a4a8a4-6419-449f-bde2-10122d90a916'),
            new NotEmptyString('text')
        );
        $answer2 = new Answer(
            Uuid::fromString('bfc153e0-8fea-489b-9010-1dfe9f9dbba8'),
            new NotEmptyString('text')
        );

        $expectedArray = [
            $answer1,
            $answer2,
        ];

        $answers = new Answers(...$expectedArray);

        $actualArray = [];
        foreach ($answers as $answer) {
            $actualArray[] = $answer;
        }

        $this->assertInstanceOf(\IteratorAggregate::class, $answers);
        $this->assertEquals($expectedArray, $actualArray);
    }
}
