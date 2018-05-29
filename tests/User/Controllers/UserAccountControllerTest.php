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
use VSV\GVQ_API\User\ValueObjects\Email;

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
     * @var SerializerInterface|MockObject
     */
    private $serializer;

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

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $this->serializer = $serializer;

        $this->userAccountController = new UserAccountController(
            $this->userRepository,
            $this->companyRepository,
            $this->serializer
        );
    }

    /**
     * @test
     */
    public function it_can_verify_login(): void
    {
        $userWithPassword = ModelsFactory::createUserWithPassword();
        $userJson = ModelsFactory::createJson('user');

        $loginDetails = ModelsFactory::createJson('login_details_correct');
        $request = new Request([], [], [], [], [], [], $loginDetails);

        $this->userRepository->expects($this->once())
            ->method('getByEmail')
            ->with(new Email('john@gvq.be'))
            ->willReturn($userWithPassword);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $userWithPassword,
                'json'
            )
            ->willReturn(
                $userJson
            );

        $actualResponse = $this->userAccountController->login($request);

        $this->assertEquals(
            $userJson,
            $actualResponse->getContent()
        );

        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function it_throws_when_no_user_found(): void
    {
        $loginDetails = ModelsFactory::createJson('login_details_correct');
        $request = new Request([], [], [], [], [], [], $loginDetails);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Login failed.');

        $this->userRepository->expects($this->once())
            ->method('getByEmail')
            ->with(new Email('john@gvq.be'))
            ->willReturn(null);

        $this->userAccountController->login($request);
    }

    /**
     * @test
     */
    public function it_throws_on_wrong_login_details(): void
    {
        $loginDetails = ModelsFactory::createJson('login_details_incorrect');
        $request = new Request([], [], [], [], [], [], $loginDetails);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Login failed.');

        $this->userRepository->expects($this->once())
            ->method('getByEmail')
            ->with(new Email('john@gvq.be'))
            ->willReturn(ModelsFactory::createUser());

        $this->userAccountController->login($request);
    }

    /**
     * @test
     */
    public function it_can_register_a_new_user(): void
    {
        $companyJsonWithoutIs = ModelsFactory::createJson('company_without_ids');
        $company = ModelsFactory::createCompany();

        $this->serializer->expects($this->once())
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
