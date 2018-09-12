<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScore;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScores;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;

class TeamScoresNormalizerTest extends TestCase
{
    /**
     * @var TeamScoresNormalizer
     */
    private $teamScoresNormalizer;

    protected function setUp(): void
    {
        $this->teamScoresNormalizer = new TeamScoresNormalizer(
            new TeamScoreNormalizer(
                new TeamNormalizer()
            )
        );
    }

    /**
     * @test
     */
    public function it_can_normalize_to_json(): void
    {
        $teamScores = ModelsFactory::createTeamScores();
        $expectedJson = ModelsFactory::createJson('team_scores');
        $actualJson = $this->teamScoresNormalizer->normalize($teamScores);

        $this->assertEquals(
            $expectedJson,
            json_encode($actualJson)
        );
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     * @param TeamScores|TeamScore $data
     * @param string $format
     */
    public function it_only_supports_answer_type_and_json_format(
        $data,
        string $format
    ): void {
        $this->assertFalse(
            $this->teamScoresNormalizer->supportsNormalization(
                $data,
                $format
            )
        );
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                ModelsFactory::createTeamScores(),
                'xml',
            ],
            [
                ModelsFactory::createLeuvenTeamScore(),
                'json',
            ],
        ];
    }
}
