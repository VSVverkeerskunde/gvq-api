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
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        $this->serializer = new UserSerializer();

        $this->userAsJson = ModelsFactory::createJson('user');

        $this->user = ModelsFactory::createUser();
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
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
    public function it_can_deserialize_to_user(): void
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
}
