<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class CategorySerializerTest extends TestCase
{
    use ExpectedJsonTrait;

    /**
     * @var CategorySerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $categoryAsJson;

    /**
     * @var Category
     */
    private $category;

    protected function setUp(): void
    {
        $this->serializer = new CategorySerializer();

        $this->categoryAsJson = $this->getExpectedJson('category.json');

        $this->category = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->category,
            'json'
        );

        $this->assertEquals(
            $this->categoryAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_category(): void
    {
        $actualCategory = $this->serializer->deserialize(
            $this->categoryAsJson,
            Category::class,
            'json'
        );

        $this->assertEquals(
            $this->category,
            $actualCategory
        );
    }
}
