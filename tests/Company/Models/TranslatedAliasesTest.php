<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\Alias;

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
            new TranslatedAlias(
                Uuid::fromString('2459ef4f-036e-41ee-8881-6254c706a7e3'),
                new Alias('abc-123'),
                new Language('nl')
            ),
            new TranslatedAlias(
                Uuid::fromString('174bc4b2-2282-485a-94ae-ff02ef12f24e'),
                new Alias('def-123'),
                new Language('nl')
            ),
        ];

        $this->translatedAliases = new TranslatedAliases(...$this->translatedAliasArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_answers(): void
    {
        $actualArray = [];
        foreach ($this->translatedAliases as $translatedAlias) {
            $actualArray[] = $translatedAlias;
        }

        $this->assertInstanceOf(\IteratorAggregate::class, $this->translatedAliases);
        $this->assertEquals($this->translatedAliasArray, $actualArray);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertInstanceOf(\Countable::class, $this->translatedAliases);
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
}
