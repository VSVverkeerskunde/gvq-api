<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    /**
     * @var Language
     */
    private $language;

    protected function setUp()
    {
        $this->language = new Language('nl');
    }

    /**
     * @return string[][]
     */
    public function languageProvider()
    {
        return [
            ['nl'],
            ['fr'],
        ];
    }

    /**
     * @test
     */
    public function it_throws_for_unsupported_languages()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Given language ge is not supported, only nl en fr are allowed.'
        );

        new Language('ge');
    }

    /**
     * @test
     */
    public function it_supports_to_native()
    {
        $this->assertEquals('nl', $this->language->toNative());
    }

    /**
     * @test
     */
    public function it_supports_to_string()
    {
        $this->assertEquals('nl', $this->language->__toString());
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
    ) {
        $this->assertEquals(
            $expected,
            $language->equals($otherLanguage)
        );
    }

    public function languagesProvider()
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
