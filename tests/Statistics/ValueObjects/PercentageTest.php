<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use PHPUnit\Framework\TestCase;

class PercentageTest extends TestCase
{
    /**
     * @var Percentage
     */
    private $percentage;

    protected function setUp(): void
    {
        $this->percentage = new Percentage(0.55);
    }

    /**
     * @test
     * @dataProvider invalidValueDataProvider
     * @param float $invalidValue
     */
    public function it_throws_on_invalid_values(float $invalidValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value should be between 0 and 1. Given value: '.$invalidValue
        );

        new Percentage($invalidValue);
    }

    /**
     * @return float[][]
     */
    public function invalidValueDataProvider(): array
    {
        return [
            [
                1.001,
            ],
            [
                -0.001,
            ],
            [
                10.0,
            ],
            [
                -10.0,
            ]
        ];
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_native(): void
    {
        $this->assertEquals(
            0.55,
            $this->percentage->toNative()
        );
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_percentage(): void
    {
        $this->assertEquals(
            new NaturalNumber(55),
            $this->percentage->toPercentage()
        );
    }
}
