<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    /**
     * @test
     */
    public function it_only_accepts_supported_languages()
    {
        $language = new Language('nl');

        $this->assertNotNull($language);

        $language = new Language('fr');
        
        $this->assertNotNull($language);
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
