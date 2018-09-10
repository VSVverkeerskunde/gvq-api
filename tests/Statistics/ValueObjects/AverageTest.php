<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use PHPUnit\Framework\TestCase;

class AverageTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_converted_to_native_value(): void
    {
        $average = new Average(11.5);

        $this->assertEquals(
            11.5,
            $average->toNative()
        );
    }

    /**
     * @test
     */
    public function it_throws_for_negative_average(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value should be positive.');

        new Average(-1.5);
    }
}
