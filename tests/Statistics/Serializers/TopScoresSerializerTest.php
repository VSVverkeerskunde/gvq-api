<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\Models\TopScores;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScoresSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var TopScores
     */
    private $topScores;

    protected function setUp(): void
    {
        $normalizers = [
            new TopScoresNormalizer(
                new TopScoreNormalizer()
            ),
        ];

        $encoders = [
            new JsonEncoder(),
            new CsvEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->topScores = new TopScores(
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('john@vsv.be'),
                new NaturalNumber(10)
            )
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_users_to_json()
    {
        $actualJson = $this->serializer->serialize(
            $this->topScores,
            'json'
        );

        $this->assertEquals(
            ModelsFactory::createJson('top_scores'),
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_serialize_users_to_csv()
    {
        $actualCsv = $this->serializer->serialize(
            $this->topScores,
            'csv'
        );

        $this->assertEquals(
            ModelsFactory::readCsv('top_scores'),
            $actualCsv
        );
    }
}
