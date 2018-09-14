<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Factory\ModelsFactory;

class ContestParticipationsSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ContestParticipations
     */
    private $contestParticipations;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $normalizers = [
            new ContestParticipationsNormalizer(
                new ContestParticipationNormalizer(
                    new ContestParticipantNormalizer(),
                    new AddressNormalizer()
                )
            ),
        ];

        $encoders = [
            new JsonEncoder(),
            new CsvEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->contestParticipations = new ContestParticipations(
            ModelsFactory::createQuizContestParticipation(),
            ModelsFactory::createCupContestParticipation()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_contest_participations_to_json()
    {
        $actualJson = $this->serializer->serialize(
            $this->contestParticipations,
            'json'
        );

        $this->assertEquals(
            ModelsFactory::createJson('contest_participations'),
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_contest_participations_to_csv()
    {
        $actualJson = $this->serializer->serialize(
            $this->contestParticipations,
            'csv'
        );

        $this->assertEquals(
            ModelsFactory::readCsv('contest_participations'),
            $actualJson
        );
    }
}
