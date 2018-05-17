<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class TranslatedAliasTest extends TestCase
{
    /**
     * @var TranslatedAlias
     */
    private $translatedAlias;

    protected function setUp(): void
    {
        $this->translatedAlias = new TranslatedAlias(
            Uuid::fromString('2459ef4f-036e-41ee-8881-6254c706a7e3'),
            new Alias('abc-123'),
            new Language('nl')
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('2459ef4f-036e-41ee-8881-6254c706a7e3'),
            $this->translatedAlias->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_alias(): void
    {
        $this->assertEquals(
            new Alias('abc-123'),
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
