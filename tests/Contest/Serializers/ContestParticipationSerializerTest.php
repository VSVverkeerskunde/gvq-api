<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;

class ContestParticipationSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new ContestParticipationNormalizer(
                new ContestParticipantNormalizer(),
                new AddressNormalizer()
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_contest_participation_to_json()
    {
        $actualJson = $this->serializer->serialize(
            ModelsFactory::createQuizContestParticipation(),
            'json'
        );

        $this->assertEquals(
            ModelsFactory::createJson('contest_participation'),
            $actualJson
        );
    }
}
