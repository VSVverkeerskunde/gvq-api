<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Models\Users;

class UsersSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Users
     */
    private $users;


    protected function setUp(): void
    {
        $normalizers = [
            new UsersNormalizer(
                new UserNormalizer()
            ),
        ];
        $encoders = [
            new JsonEncoder(),
            new CsvEncoder()
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->users = new Users(
            ModelsFactory::createUser(),
            ModelsFactory::createAlternateUser(),
            ModelsFactory::createFrenchUser()
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_a_users_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->users,
            'json'
        );

        $this->assertEquals(
            ModelsFactory::createJson('users'),
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_a_users_to_csv(): void
    {
        $actualCsv = $this->serializer->serialize(
            $this->users,
            'csv'
        );

        $this->assertEquals(
            ModelsFactory::readCsv('users'),
            $actualCsv
        );
    }
}
