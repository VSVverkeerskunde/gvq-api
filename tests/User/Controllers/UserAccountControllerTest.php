<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\Serializers\CompanySerializer;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\Serializers\UserSerializer;

class UserAccountControllerTest extends TestCase
{
    /**
     * @var UserRepository|MockObject
     */
    private $userRepository;

    /**
     * @var CompanyRepository|MockObject
     */
    private $companyRepository;

    /**
     * @var CompanySerializer|MockObject
     */
    private $companySerializer;

    /**
     * @var UserAccountController
     */
    private $userAccountController;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        /** @var UserRepository|MockObject $userRepository */
        $userRepository = $this->createMock(UserRepository::class);
        $this->userRepository = $userRepository;

        /** @var CompanyRepository|MockObject $companyRepository */
        $companyRepository = $this->createMock(CompanyRepository::class);
        $this->companyRepository = $companyRepository;

        /** @var CompanySerializer|MockObject $companySerializer */
        $companySerializer = $this->createMock(CompanySerializer::class);
        $this->companySerializer = $companySerializer;

        $this->userAccountController = new UserAccountController(
            $this->userRepository,
            new UserSerializer(),
            $this->companyRepository,
            new CompanySerializer()
        );
    }

    /**
     * @test
     * @dataProvider loginDetailsProvider
     * @param string $input
     * @param Response $expectedResponse
     */
    public function it_can_verify_login(string $input, Response $expectedResponse): void
    {
        $user = ModelsFactory::createUser();

        $expectedResponse->headers->set('Content-Type', 'application/json');

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $request = new Request([], [], [], [], [], [], $input);
        $actualResponse = $this->userAccountController->login($request);

        $this->assertEquals(
            $expectedResponse->getContent(),
            $actualResponse->getContent()
        );

        $this->assertEquals(
            $expectedResponse->headers->get('Content-Type'),
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @return array[]
     */
    public function loginDetailsProvider(): array
    {
        return [
            [
                ModelsFactory::createJson('login_details_correct'),
                new Response(ModelsFactory::createJson('user')),
            ],
            [
                ModelsFactory::createJson('login_details_incorrect'),
                new Response('{"id":"null"}'),
            ],
        ];
    }

    /**
     * @test
     */
    public function it_can_register_a_new_user(): void
    {
        $companyJson = ModelsFactory::createJson('company_without_ids');
        $companySerializer = new CompanySerializer();
        /** @var Company $company */
        $company = $companySerializer->deserialize($companyJson, Company::class, 'json');

        $expectedResponse = new Response('{"id":"'.$company->getId()->toString().'"}');
        $expectedResponse->headers->set('Content-Type', 'application/json');

        $request = new Request([], [], [], [], [], [], $companyJson);
        $actualResponse = $this->userAccountController->register($request);

        $this->assertEquals(
            $expectedResponse->getContent(),
            $actualResponse->getContent()
        );

        $this->assertEquals(
            $expectedResponse->headers->get('Content-Type'),
            $actualResponse->headers->get('Content-Type')
        );
    }
}
