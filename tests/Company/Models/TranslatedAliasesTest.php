<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Factory\ModelsFactory;

class TranslatedAliasesTest extends TestCase
{
    /**
     * @var TranslatedAlias[]
     */
    private $translatedAliasArray;

    /**
     * @var TranslatedAliases
     */
    private $translatedAliases;

    protected function setUp(): void
    {
        $this->translatedAliasArray = [
            ModelsFactory::createNlAlias(),
            ModelsFactory::createFrAlias(),
        ];

        $this->translatedAliases = new TranslatedAliases(...$this->translatedAliasArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_translated_aliases(): void
    {
        $actualArray = [];
        foreach ($this->translatedAliases as $translatedAlias) {
            $actualArray[] = $translatedAlias;
        }

        $this->assertEquals($this->translatedAliasArray, $actualArray);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(2, count($this->translatedAliases));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->translatedAliasArray,
            $this->translatedAliases->toArray()
        );
    }

    /**
     * @test
     */
    public function it_can_get_an_alias_by_language(): void
    {
        $this->assertEquals(
            ModelsFactory::createFrAlias(),
            $this->translatedAliases->getByLanguage(new Language('fr'))
        );
    }
}
