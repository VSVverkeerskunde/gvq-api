<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration;

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
    public function it_generates_a_string(): void
    {
        $urlSuffix = $this->urlSuffixHashGenerator->createUrlSuffix();

        $this->assertEquals(
            22,
            strlen($urlSuffix->toNative())
        );

        $this->assertInternalType(
            'string',
            $urlSuffix->toNative()
        );
    }
}
