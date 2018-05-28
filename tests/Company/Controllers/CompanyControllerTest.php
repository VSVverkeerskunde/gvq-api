<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\Serializers\CompanySerializer;
use VSV\GVQ_API\Factory\ModelsFactory;

class CompanyControllerTest extends TestCase
{
    /**
     * @var CompanyRepository|MockObject
     */
    private $companyRepository;

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
        $companyRepository = $this->createMock(
            CompanyRepository::class
        );
        $this->companyRepository = $companyRepository;

        $this->companyController = new CompanyController(
            $this->companyRepository,
            new CompanySerializer()
        );
    }

    /**
     * @test
     */
    public function it_saves_a_user(): void
    {
        $userJson = ModelsFactory::createJson('company');
        $request = new Request([], [], [], [], [], [], $userJson);

        $userSerializer = new CompanySerializer();
        /** @var Company $company */
        $company = $userSerializer->deserialize(
            $userJson,
            Company::class,
            'json'
        );

        $this->companyRepository
            ->expects($this->once())
            ->method('save')
            ->with($company);

        $expectedResponse = new Response('{"id":"'.$company->getId()->toString().'"}');
        $expectedResponse->headers->set('Content-Type', 'application/json');

        $actualResponse = $this->companyController->save($request);

        $this->assertEquals(
            $expectedResponse,
            $actualResponse
        );
        $this->assertEquals(
            $expectedResponse->headers->get('Content-Type'),
            $actualResponse->headers->get('Content-Type')
        );
    }
}
