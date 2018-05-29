<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Models\User;

class UserSerializerTest extends TestCase
{
    /**
     * @var UserSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $userAsJson;

    /**
     * @var string
     */
    private $userWithPasswordAsJson;

    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $userWithPassword;

    protected function setUp(): void
    {
        $this->serializer = new UserSerializer();

        $this->userAsJson = ModelsFactory::createJson('user');
        $this->userWithPasswordAsJson = ModelsFactory::createJson('user_with_password');

        $this->user = ModelsFactory::createUser();
        $this->userWithPassword = ModelsFactory::createUserWithPassword();
    }

    /**
     * @test
     */
    public function it_can_serialize_a_user_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->user,
            'json'
        );

        $this->assertEquals(
            $this->userAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_a_user_with_password_to_json_without_password(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->userWithPassword,
            'json'
        );

        $this->assertEquals(
            $this->userAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_json_to_user(): void
    {
        $actualUser = $this->serializer->deserialize(
            $this->userAsJson,
            User::class,
            'json'
        );

        $this->assertEquals(
            $this->user,
            $actualUser
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_json_with_password_to_user_with_password(): void
    {
        /** @var User $actualUser */
        $actualUser = $this->serializer->deserialize(
            $this->userWithPasswordAsJson,
            User::class,
            'json'
        );

        $this->assertEquals(
            $this->userWithPassword->getId(),
            $actualUser->getId()
        );
        $this->assertEquals(
            $this->userWithPassword->getEmail(),
            $actualUser->getEmail()
        );
        $this->assertEquals(
            $this->userWithPassword->getLastName(),
            $actualUser->getLastName()
        );
        $this->assertEquals(
            $this->userWithPassword->getRole(),
            $actualUser->getRole()
        );
        $this->assertNotNull(
            $actualUser->getPassword()
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_user_when_ids_are_missing(): void
    {
        $userAsJson = ModelsFactory::createJson('user_without_id');

        /** @var User $actualUser */
        $actualUser = $this->serializer->deserialize(
            $userAsJson,
            User::class,
            'json'
        );

        $this->assertNotNull($actualUser->getId());
    }
}
