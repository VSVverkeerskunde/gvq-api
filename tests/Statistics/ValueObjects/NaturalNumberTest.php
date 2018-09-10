<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use PHPUnit\Framework\TestCase;

class NaturalNumberTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_return_a_native_value(): void
    {
        $naturalNumber = new NaturalNumber(10);

        $this->assertEquals(
            10,
            $naturalNumber->toNative()
        );
    }

    /**
     * @test
     * @dataProvider naturalNumberProvider
     * @param NaturalNumber $n1
     * @param NaturalNumber $n2
     * @param bool $equal
     */
    public function it_can_be_compared(
        NaturalNumber $n1,
        NaturalNumber $n2,
        bool $equal
    ): void {
        $this->assertEquals(
            $equal,
            $n1->equals($n2)
        );
    }

    /**
     * @return array
     */
    public function naturalNumberProvider(): array
    {
        return [
            [
                new NaturalNumber(10),
                new NaturalNumber(10),
                true,
            ],
            [
                new NaturalNumber(10),
                new NaturalNumber(9),
                false,
            ]
        ];
    }

    /**
     * @test
     */
    public function it_throws_for_negative_values(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value has to be 0 or greater, -1 given.');

        new NaturalNumber(-1);
    }
}
