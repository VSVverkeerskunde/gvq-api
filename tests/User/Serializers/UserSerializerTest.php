<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Models\User;

class UserSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
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
        $normalizers = [
            new UserNormalizer(),
            new UserDenormalizer(),
        ];
        $encoders = [
            new JsonEncoder(),
            new CsvEncoder()
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

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
    public function it_can_serialize_a_user_to_csv(): void
    {
        $actualCsv = $this->serializer->serialize(
            $this->user,
            'csv'
        );

        $userAsCsv = ModelsFactory::readCsv('user');
        $this->assertEquals(
            $userAsCsv,
            $actualCsv
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
}
