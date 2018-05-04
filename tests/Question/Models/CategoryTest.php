<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPStan\Testing\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    private $category;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->category = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id()
    {
        $this->assertEquals(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            $this->category->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_name()
    {
        $this->assertEquals(
            new NotEmptyString('EHBO/Ongeval/Verzekering'),
            $this->category->getName()
        );
    }
}
