<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class CompaniesSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Companies
     */
    private $companies;

    protected function setUp(): void
    {
        $normalizers = [
            new CompaniesNormalizer(
                new CompanyNormalizer(
                    new TranslatedAliasNormalizer(),
                    new UserNormalizer()
                )
            ),
        ];

        $encoders = [
            new JsonEncoder(),
            new CsvEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->companies = new Companies(
            ModelsFactory::createCompany(),
            ModelsFactory::createAlternateCompany()
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_users_to_json()
    {
        $actualJson = $this->serializer->serialize(
            $this->companies,
            'json'
        );

        $this->assertEquals(
            ModelsFactory::createJson('companies'),
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_users_to_csv()
    {
        $actualCsv = $this->serializer->serialize(
            $this->companies,
            'csv'
        );

        $this->assertEquals(
            ModelsFactory::readCsv('companies'),
            $actualCsv
        );
    }
}
