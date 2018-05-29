<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Repositories\UserRepository;

class UserAccountControllerTest extends TestCase
{
    /**
     * @var UserRepository|MockObject
     */
    private $userRepository;

    /**
     * @var SerializerInterface|MockObject
     */
    private $userSerializer;

    /**
     * @var CompanyRepository|MockObject
     */
    private $companyRepository;

    /**
     * @var SerializerInterface|MockObject
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

        /** @var SerializerInterface|MockObject $userSerializer */
        $userSerializer = $this->createMock(SerializerInterface::class);
        $this->userSerializer = $userSerializer;

        /** @var CompanyRepository|MockObject $companyRepository */
        $companyRepository = $this->createMock(CompanyRepository::class);
        $this->companyRepository = $companyRepository;

        /** @var SerializerInterface|MockObject $companySerializer */
        $companySerializer = $this->createMock(SerializerInterface::class);
        $this->companySerializer = $companySerializer;

        $this->userAccountController = new UserAccountController(
            $this->userRepository,
            $this->userSerializer,
            $this->companyRepository,
            $this->companySerializer
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
        $userJson = ModelsFactory::createJson('user');

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $this->userSerializer
            ->method('serialize')
            ->willReturn($userJson);

        $request = new Request([], [], [], [], [], [], $input);
        $actualResponse = $this->userAccountController->login($request);

        $this->assertEquals(
            $expectedResponse->getContent(),
            $actualResponse->getContent()
        );

        $this->assertEquals(
            'application/json',
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
        $companyJsonWithoutIs = ModelsFactory::createJson('company_without_ids');
        $company = ModelsFactory::createCompany();

        $this->companySerializer->expects($this->once())
            ->method('deserialize')
            ->with(
                $companyJsonWithoutIs,
                Company::class,
                'json'
            )
            ->willReturn($company);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($company->getUser());

        $this->companyRepository->expects($this->once())
            ->method('save')
            ->with($company);

        $request = new Request([], [], [], [], [], [], $companyJsonWithoutIs);
        $actualResponse = $this->userAccountController->register($request);

        $expectedResponse = new Response('{"id":"'.$company->getUser()->getId()->toString().'"}');
        $expectedResponse->headers->set('Content-Type', 'application/json');

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
