<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

use PHPUnit\Framework\TestCase;

class PositiveNumberTest extends TestCase
{
    /**
     * @var PositiveNumber
     */
    private $positiveNumber;

    protected function setUp(): void
    {
        $this->positiveNumber = new PositiveNumber(49);
    }

    /**
     * @test
     * @dataProvider invalidNumberProvider
     * @param int $positiveNumber
     */
    public function it_throws_for_unsupported_values(int $positiveNumber): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value has to be greater than 0, '.$positiveNumber.' given.'
        );

        new PositiveNumber($positiveNumber);
    }

    /**
     * @return int[][]
     */
    public function invalidNumberProvider(): array
    {
        return [
            [
                -5,
            ],
            [
                0,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider numbersProvider
     * @param PositiveNumber $positiveNumber
     * @param PositiveNumber $otherPositiveNumber
     * @param bool $expected
     */
    public function it_supports_equal_function(
        PositiveNumber $positiveNumber,
        PositiveNumber $otherPositiveNumber,
        bool $expected
    ): void {
        $this->assertEquals(
            $expected,
            $positiveNumber->equals($otherPositiveNumber)
        );
    }

    /**
     * @return array[]
     */
    public function numbersProvider(): array
    {
        return [
            [
                new PositiveNumber(2050),
                new PositiveNumber(2050),
                true,
            ],
            [
                new PositiveNumber(2050),
                new PositiveNumber(2051),
                false,
            ],
        ];
    }
}
