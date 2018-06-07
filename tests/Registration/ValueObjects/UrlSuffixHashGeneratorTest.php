<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\ValueObjects;

use PHPUnit\Framework\TestCase;

class UrlSuffixHashGeneratorTest extends TestCase
{
    /**
     * @var UrlSuffixHashGenerator
     */
    private $urlSuffixHashGenerator;

    protected function Setup(): void
    {
        $this->urlSuffixHashGenerator = new UrlSuffixHashGenerator();
    }

    /**
     * @test
     */
    public function it_generates_a_suffix(): void
    {
        $urlSuffix = $this->urlSuffixHashGenerator->createUrlSuffix();

        $this->assertNotNull($urlSuffix);
    }

    /**
     * @test
     */
    public function it_generates_unique_suffixes(): void
    {
        $urlSuffix = $this->urlSuffixHashGenerator->createUrlSuffix();
        $otherUrlSuffix = $this->urlSuffixHashGenerator->createUrlSuffix();

        $this->assertNotEquals($urlSuffix, $otherUrlSuffix);
    }
}
