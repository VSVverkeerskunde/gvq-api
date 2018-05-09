<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class AnswerTest extends TestCase
{
    /**
     * @var Answer
     */
    private $answer;

    protected function setUp(): void
    {
        $this->answer = new Answer(
            Uuid::fromString('b7322f69-98cf-4ec4-a551-5d6661fffc17'),
            new NotEmptyString('This is the first answer.'),
            true
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('b7322f69-98cf-4ec4-a551-5d6661fffc17'),
            $this->answer->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_text(): void
    {
        $this->assertEquals(
            new NotEmptyString('This is the first answer.'),
            $this->answer->getText()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_is_correct_flag(): void
    {
        $this->assertTrue($this->answer->isCorrect());
    }
}
