<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class AnswersTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_iterate_over_answers()
    {
        $answer1 = new Answer(
            Uuid::fromString('b1a4a8a4-6419-449f-bde2-10122d90a916'),
            new NotEmptyString('text'),
            false
        );
        $answer2 = new Answer(
            Uuid::fromString('bfc153e0-8fea-489b-9010-1dfe9f9dbba8'),
            new NotEmptyString('text'),
            false
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
