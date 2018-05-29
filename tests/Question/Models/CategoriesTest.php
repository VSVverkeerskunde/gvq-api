<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class CategoriesTest extends TestCase
{
    /**
     * @var Category[]
     */
    private $categoriesArray;

    /**
     * @var Categories
     */
    private $categories;

    protected function setUp(): void
    {
        $this->categoriesArray = [
            new Category(
                Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
                new NotEmptyString('EHBO/Ongeval/Verzekering')
            ),
            new Category(
                Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
                new NotEmptyString('Algemene verkeersregels')
            )
        ];

        $this->categories = new Categories(...$this->categoriesArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_categories(): void
    {
        $actualCategories = [];
        foreach ($this->categories as $category) {
            $actualCategories[] = $category;
        }

        $this->assertInstanceOf(\IteratorAggregate::class, $this->categories);
        $this->assertEquals($this->categoriesArray, $actualCategories);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertInstanceOf(\Countable::class, $this->categories);
        $this->assertEquals(2, count($this->categories));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->categoriesArray,
            $this->categories->toArray()
        );
    }
}
