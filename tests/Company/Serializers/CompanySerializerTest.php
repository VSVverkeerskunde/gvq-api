<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class CompanySerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $companyAsJson;

    /**
     * @var Company
     */
    private $company;

    protected function setUp(): void
    {
        $normalizers = [
            new CompanyNormalizer(
                new TranslatedAliasNormalizer(),
                new UserNormalizer()
            ),
            new CompanyDenormalizer(
                new TranslatedAliasDenormalizer(),
                new UserDenormalizer()
            ),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->companyAsJson = ModelsFactory::createJson('company');

        $this->company = ModelsFactory::createCompany();
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->company,
            'json'
        );

        $this->assertEquals(
            $this->companyAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_company(): void
    {
        $actualCompany = $this->serializer->deserialize(
            $this->companyAsJson,
            Company::class,
            'json'
        );

        $this->assertEquals(
            $this->company,
            $actualCompany
        );
    }
}
