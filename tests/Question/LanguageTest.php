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
}
