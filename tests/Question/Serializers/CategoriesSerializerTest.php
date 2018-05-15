<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class CategoriesSerializerTest extends TestCase
{
    use ExpectedJsonTrait;

    /**
     * @var CategoriesSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $categoriesAsJson;

    /**
     * @var Categories
     */
    private $categories;

    protected function setUp(): void
    {
        $this->serializer = new CategoriesSerializer();

        $this->categoriesAsJson = $this->getExpectedJson(__DIR__ . '/Samples/categories.json');

        $this->categories = new Categories(
            new Category(
                Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
                new NotEmptyString('EHBO/Ongeval/Verzekering')
            ),
            new Category(
                Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
                new NotEmptyString('Algemene verkeersregels')
            )
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->categories,
            'json'
        );

        $this->assertEquals(
            $this->categoriesAsJson,
            $actualJson
        );
    }
}
