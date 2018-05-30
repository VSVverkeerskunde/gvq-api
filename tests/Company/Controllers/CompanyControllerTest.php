<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Factory\ModelsFactory;

class CompanyControllerTest extends TestCase
{
    /**
     * @var CompanyRepository|MockObject
     */
    private $companyRepository;

    /**
     * @var SerializerInterface|MockObject
     */
    private $companySerializer;

    /**
     * @var JsonEnricher|MockObject
     */
    private $jsonEnricher;

    /**
     * @var CompanyController
     */
    private $companyController;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        /** @var CompanyRepository|MockObject $companyRepository */
        $companyRepository = $this->createMock(CompanyRepository::class);
        $this->companyRepository = $companyRepository;

        /** @var SerializerInterface|MockObject $companySerializer */
        $companySerializer = $this->createMock(SerializerInterface::class);
        $this->companySerializer = $companySerializer;

        /** @var JsonEnricher|MockObject $jsonEnricher */
        $jsonEnricher = $this->createMock(JsonEnricher::class);
        $this->jsonEnricher = $jsonEnricher;

        $this->companyController = new CompanyController(
            $this->companyRepository,
            $this->companySerializer,
            $this->jsonEnricher
        );
    }

    /**
     * @test
     */
    public function it_saves_a_company(): void
    {
        $newCompanyJson = ModelsFactory::createJson('new_company');
        $companyJson = ModelsFactory::createJson('company');
        $company = ModelsFactory::createCompany();

        $this->jsonEnricher
            ->expects($this->once())
            ->method('enrich')
            ->willReturn($companyJson);

        $this->companySerializer
            ->expects($this->once())
            ->method('deserialize')
            ->with(
                $companyJson,
                Company::class,
                'json'
            )
            ->willReturn($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('save')
            ->with($company);

        $request = new Request([], [], [], [], [], [], $newCompanyJson);
        $actualResponse = $this->companyController->save($request);

        $this->assertEquals(
            '{"id":"'.$company->getId()->toString().'"}',
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }
}
