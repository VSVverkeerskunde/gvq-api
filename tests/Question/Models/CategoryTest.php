<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;

class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    private $category;

    protected function setUp(): void
    {
        $this->category = ModelsFactory::createAccidentCategory();
    }

    /**
     * @test
     */
    public function it_stores_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            $this->category->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('EHBO/Ongeval/Verzekering'),
            $this->category->getName()
        );
    }
}
