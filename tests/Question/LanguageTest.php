<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{

    /**
     * @test
     *
     * @dataProvider languageProvider
     * @param string $languageAsString
     */
    public function it_only_accepts_supported_languages(string $languageAsString)
    {
        $language = new Language($languageAsString);

        $this->assertNotNull($language);
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
}
