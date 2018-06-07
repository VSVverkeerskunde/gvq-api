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
     * @dataProvider invalidUrlSuffixProvider
     * @param string $value
     */
    public function it_throws_for_unsupported_values(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value has to be at least 22 characters long, '.strlen($value).' given.');

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
}
