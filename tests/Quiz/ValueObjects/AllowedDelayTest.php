<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use PHPUnit\Framework\TestCase;

class AllowedDelayTest extends TestCase
{
    /**
     * @test
     * @dataProvider unsupportedProvider
     * @param int $value
     */
    public function it_throws_for_unsupported_values(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Allowed delay should be a positive number.');

        new AllowedDelay($value);
    }

    /**
     * @return int[][]
     */
    public function unsupportedProvider(): array
    {
        return [
            'zero' => [
                0,
            ],
            'negative' => [
                -1,
            ],
        ];
    }

    /**
     * @test
     */
    public function it_supports_to_native(): void
    {
        $allowedDelay = new AllowedDelay(40);

        $this->assertEquals(
            40,
            $allowedDelay->toNative()
        );
    }
}
