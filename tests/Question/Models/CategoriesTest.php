<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

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
            ModelsFactory::createAccidentCategory(),
            ModelsFactory::createGeneralCategory(),
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
