<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\Serializers\UserSerializer;

class UserControllerTest extends TestCase
{
    /**
     * @var UserRepository|MockObject
     */
    private $userRepository;

    /**
     * @var UserController
     */
    private $userController;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        /** @var UserRepository|MockObject $userRepository */
        $userRepository = $this->createMock(
            UserRepository::class
        );
        $this->userRepository = $userRepository;

        $this->userController = new UserController(
            $this->userRepository,
            new UserSerializer()
        );
    }

    /**
     * @test
     */
    public function it_saves_a_user(): void
    {
        $userJson = ModelsFactory::createJson('user');
        $request = new Request([], [], [], [], [], [], $userJson);

        $userSerializer = new UserSerializer();
        /** @var User $user */
        $user = $userSerializer->deserialize(
            $userJson,
            User::class,
            'json'
        );

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $expectedResponse = new Response('{"id":"'.$user->getId()->toString().'"}');
        $expectedResponse->headers->set('Content-Type', 'application/json');

        $actualResponse = $this->userController->save($request);

        $this->assertEquals(
            $expectedResponse,
            $actualResponse
        );
        $this->assertEquals(
            $expectedResponse->headers->get('Content-Type'),
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @test
     * @dataProvider loginDetailsProvider
     * @param string $input
     * @param string $expectedResponse
     */
    public function it_can_verify_login(string $input, string $expectedResponse): void
    {
        //$userJson = ModelsFactory::createJson('user');
        $user = ModelsFactory::createUser();

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $request = new Request([], [], [], [], [], [], $input);
        $actualResponse = $this->userController->login($request);

        $this->assertEquals(
            $expectedResponse,
            $actualResponse->getContent()
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
                ModelsFactory::createJson('user'),
            ],
            [
                ModelsFactory::createJson('login_details_incorrect'),
                '{"id":"null"}',
            ],
        ];
    }
}
