<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Team\Models\Team;

class TeamSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $teamAsJson;

    /**
     * @var Team
     */
    private $team;

    protected function setUp(): void
    {
        $normalizers = [
            new TeamNormalizer(),
            new TeamDenormalizer(),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->teamAsJson = ModelsFactory::createJson('team');
        $this->team = ModelsFactory::createAntwerpTeam();
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->team,
            'json'
        );

        $this->assertEquals(
            $this->teamAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_partner(): void
    {
        $actualTeam = $this->serializer->deserialize(
            $this->teamAsJson,
            Team::class,
            'json'
        );

        $this->assertEquals(
            $this->team,
            $actualTeam
        );
    }
}
