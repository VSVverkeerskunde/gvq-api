<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use PHPUnit\Framework\TestCase;

class QuizTypeTest extends TestCase
{
    /**
     * @var QuizType
     */
    private $quizType;

    protected function setUp(): void
    {
        $this->quizType = new QuizType('quiz');
    }

    /**
     * @test
     * @dataProvider invalidTypeProvider
     * @param string $type
     */
    public function it_throws_for_unsupported_values(string $type): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid value '.$type.' for quiz type'
        );

        new QuizType($type);
    }

    /**
     * @return string [][]
     */
    public function invalidTypeProvider(): array
    {
        return [
            [
                'wrong',
            ],
            [
                'incorrect',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validTypeProvider
     * @param string $type
     */
    public function it_supports_allowed_values(string $type): void
    {
        $this->assertNotNull(
            new QuizType($type)
        );
    }

    /**
     * @return string[][]
     */
    public function validTypeProvider(): array
    {
        return [
            [
                'quiz',
            ],
            [
                'cup',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_supports_to_native(): void
    {
        $this->assertSame(
            'quiz',
            $this->quizType->toNative()
        );
    }

    /**
     * @test
     * @dataProvider typesProvider
     * @param QuizType $type
     * @param QuizType $otherType
     * @param bool $expected
     */
    public function it_supports_equal_function(
        QuizType $type,
        QuizType $otherType,
        bool $expected
    ): void {
        $this->assertEquals(
            $expected,
            $type->equals($otherType)
        );
    }

    /**
     * @return array[]
     */
    public function typesProvider(): array
    {
        return [
            [
                new QuizType('quiz'),
                new QuizType('quiz'),
                true,
            ],
            [
                new QuizType('quiz'),
                new QuizType('cup'),
                false,
            ],
        ];
    }
}
