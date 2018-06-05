<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

use PHPUnit\Framework\TestCase;

class LanguagesTest extends TestCase
{
    /**
     * @var Language[]
     */
    private $languagesArray;

    /**
     * @var Languages
     */
    private $languages;

    protected function setUp(): void
    {
        $this->languagesArray = [
            new Language('nl'),
            new Language('fr'),
        ];

        $this->languages = new Languages();
    }

    /**
     * @test
     */
    public function it_can_iterate_over_categories(): void
    {
        $actualLanguages = [];
        foreach ($this->languages as $language) {
            $actualLanguages[] = $language;
        }

        $this->assertInstanceOf(\IteratorAggregate::class, $this->languages);
        $this->assertEquals($this->languagesArray, $actualLanguages);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertInstanceOf(\Countable::class, $this->languages);
        $this->assertEquals(2, count($this->languages));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->languagesArray,
            $this->languages->toArray()
        );
    }
}
