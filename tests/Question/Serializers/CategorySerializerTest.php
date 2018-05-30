<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Category;

class CategorySerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
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
        $normalizers = [
            new CategoryNormalizer(),
            new CategoryDenormalizer(),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->categoryAsJson = ModelsFactory::createJson('category');

        $this->category = ModelsFactory::createAccidentCategory();
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
