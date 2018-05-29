<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;

class UserControllerTest extends TestCase
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
     * @var UserController
     */
    private $userController;

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

        $this->userController = new UserController(
            $this->userRepository,
            $this->userSerializer
        );
    }

    /**
     * @test
     */
    public function it_saves_a_user(): void
    {
        $userJson = ModelsFactory::createJson('user');
        $user = ModelsFactory::createUser();

        $this->userSerializer->expects($this->once())
            ->method('deserialize')
            ->with(
                $userJson,
                User::class,
                'json'
            )
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $expectedResponse = new Response('{"id":"'.$user->getId()->toString().'"}');

        $request = new Request([], [], [], [], [], [], $userJson);
        $actualResponse = $this->userController->save($request);

        $this->assertEquals(
            $expectedResponse->getContent(),
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }
}
