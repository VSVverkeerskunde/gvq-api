<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Factory\ModelsFactory;

class TranslatedAliasTest extends TestCase
{
    /**
     * @var TranslatedAlias
     */
    private $translatedAlias;

    protected function setUp(): void
    {
        $this->translatedAlias = ModelsFactory::createNlAlias();
    }

    /**
     * @test
     */
    public function it_stores_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('827a7945-ffd0-433e-b843-721c98ab72b8'),
            $this->translatedAlias->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_alias(): void
    {
        $this->assertEquals(
            new Alias('company-name-nl'),
            $this->translatedAlias->getAlias()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_language(): void
    {
        $this->assertEquals(
            new Language('nl'),
            $this->translatedAlias->getLanguage()
        );
    }
}
