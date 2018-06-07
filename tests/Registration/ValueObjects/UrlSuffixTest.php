<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\ValueObjects;

use PHPUnit\Framework\TestCase;

class UrlSuffixTest extends TestCase
{
    /**
     * @var UrlSuffix
     */
    private $urlSuffix;

    protected function setUp(): void
    {
        $this->urlSuffix = new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef');
    }

    /**
     * @test
     */
    public function it_supports_to_native()
    {
        $this->assertEquals(
            'd2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef',
            $this->urlSuffix->toNative()
        );
    }

    /**
     * @test
     * @dataProvider invalidUrlSuffixProvider
     * @param string $value
     */
    public function it_throws_for_unsupported_values(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value has to be at least 44 characters long, '.strlen($value).' given.');

        new UrlSuffix($value);
    }

    /**
     * @return string[][]
     */
    public function invalidUrlSuffixProvider(): array
    {
        return [
            [
                'dizhnfe5e5dd',
            ],
            [
                '',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider urlSuffixProvider
     * @param UrlSuffix $urlSuffix
     * @param UrlSuffix $otherUrlSuffix
     * @param bool $expected
     */
    public function it_supports_equals_function(
        UrlSuffix $urlSuffix,
        UrlSuffix $otherUrlSuffix,
        bool $expected
    ): void {
        $this->assertEquals(
            $expected,
            $urlSuffix->equals($otherUrlSuffix)
        );
    }

    /**
     * @return array[]
     */
    public function urlSuffixProvider(): array
    {
        return [
            [
                new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef'),
                new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef'),
                true,
            ],
            [
                new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef'),
                new UrlSuffix('d11c68e5d2c38329e9040fcbbdd9ae66ece6185d907c'),
                false,
            ],
        ];
    }
}
