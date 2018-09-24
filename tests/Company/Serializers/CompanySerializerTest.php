<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class CompanySerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

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
    }

    /**
     * @test
     * @dataProvider companyDataProvider
     * @param Company $company
     * @param string $companyAsJson
     */
    public function it_can_serialize_to_json(
        Company $company,
        string $companyAsJson
    ): void {
        $actualJson = $this->serializer->serialize(
            $company,
            'json'
        );

        $this->assertEquals(
            $companyAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @dataProvider companyDataProvider
     * @param Company $company
     * @param string $companyAsJson
     */
    public function it_can_deserialize_to_company(
        Company $company,
        string $companyAsJson
    ): void {
        $actualCompany = $this->serializer->deserialize(
            $companyAsJson,
            Company::class,
            'json'
        );

        $this->assertEquals(
            $company,
            $actualCompany
        );
    }

    /**
     * @return array
     */
    public function companyDataProvider(): array
    {
        return [
            [
                ModelsFactory::createCompany(),
                ModelsFactory::createJson('company'),
            ],
            [
                ModelsFactory::createCompany()->withNrOfPassedEmployees(
                    new NaturalNumber(4)
                ),
                ModelsFactory::createJson(
                    'company_with_nr_of_passed_employees'
                ),
            ],
        ];
    }
}
