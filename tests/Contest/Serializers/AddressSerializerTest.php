<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;

class AddressSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new AddressNormalizer(),
        ];

        $encoders = [
            new JsonEncoder(),
            new CsvEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $address = ModelsFactory::createVsvAddress();
        $actualJson = $this->serializer->serialize($address, 'json');

        $this->assertEquals(
            ModelsFactory::createJson('address'),
            $actualJson
        );
    }
}
