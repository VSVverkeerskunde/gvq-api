<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partner;

class PartnerSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $partnerAsJson;

    /**
     * @var Partner
     */
    private $partner;

    protected function setUp(): void
    {
        $normalizers = [
            new PartnerNormalizer(),
            new PartnerDenormalizer(),
        ];
        
        $encoders = [
            new JsonEncoder(),
        ];
        
        $this->serializer = new Serializer($normalizers, $encoders);
        
        $this->partnerAsJson = ModelsFactory::createJson('partner');
        $this->partner = ModelsFactory::createNBPartner();
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->partner,
            'json'
        );

        $this->assertEquals(
            $this->partnerAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_partner(): void
    {
        $actualpartner = $this->serializer->deserialize(
            $this->partnerAsJson,
            partner::class,
            'json'
        );

        $this->assertEquals(
            $this->partner,
            $actualpartner
        );
    }
}
