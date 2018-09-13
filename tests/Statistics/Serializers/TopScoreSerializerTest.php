<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScoreSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $topScoreAsJson;

    /**
     * @var TopScore
     */
    private $topScore;

    protected function setUp(): void
    {
        $normalizers = [
            new TopScoreNormalizer(),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->topScoreAsJson = ModelsFactory::createJson('top_score');

        $this->topScore = new TopScore(
            new Email('jane@vsv.be'),
            new NaturalNumber(11)
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->topScore,
            'json'
        );

        $this->assertEquals(
            $this->topScoreAsJson,
            $actualJson
        );
    }
}
