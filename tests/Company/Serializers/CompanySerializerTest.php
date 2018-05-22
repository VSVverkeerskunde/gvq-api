<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Factory\ModelsFactory;

class CompanySerializerTest extends TestCase
{
    /**
     * @var CompanySerializer
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
        $this->serializer = new CompanySerializer();

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
