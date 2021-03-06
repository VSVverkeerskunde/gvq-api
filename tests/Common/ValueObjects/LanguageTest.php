<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    /**
     * @var Language
     */
    private $language;

    protected function setUp(): void
    {
        $this->language = new Language('nl');
    }

    /**
     * @test
     */
    public function it_throws_for_unsupported_languages(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid value "ge" for VSV\GVQ_API\Common\ValueObjects\Language.'
        );

        new Language('ge');
    }

    /**
     * @test
     */
    public function it_supports_to_native(): void
    {
        $this->assertEquals(
            'nl',
            $this->language->toNative()
        );
    }

    /**
     * @test
     * @dataProvider languagesProvider
     * @param Language $language
     * @param Language $otherLanguage
     * @param bool $expected
     */
    public function it_supports_equals_function(
        Language $language,
        Language $otherLanguage,
        bool $expected
    ): void {
        $this->assertEquals(
            $expected,
            $language->equals($otherLanguage)
        );
    }

    /**
     * @return array
     */
    public function languagesProvider(): array
    {
        return [
            [
                new Language('nl'),
                new Language('nl'),
                true,
            ],
            [
                new Language('nl'),
                new Language('fr'),
                false,
            ],
        ];
    }
}
