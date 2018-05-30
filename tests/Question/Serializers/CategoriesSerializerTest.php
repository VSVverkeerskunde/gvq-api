<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Categories;

class CategoriesSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
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
        $normalizers = [
            new CategoriesNormalizer(
                new CategoryNormalizer()
            ),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->categoriesAsJson = ModelsFactory::createJson('categories');
        $this->categories = ModelsFactory::createCategories();
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
