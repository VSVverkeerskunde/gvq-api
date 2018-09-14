<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;

class ContestParticipantSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new ContestParticipantNormalizer(),
        ];

        $encoders = [
            new JsonEncoder(),
            new CsvEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_to_json(): void
    {
        $contestParticipant = ModelsFactory::createContestParticipant();
        $actualJson = $this->serializer->serialize($contestParticipant, 'json');

        $this->assertEquals(
            ModelsFactory::createJson('contest_participant'),
            $actualJson
        );
    }
}
