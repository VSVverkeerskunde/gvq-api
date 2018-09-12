<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScore;
use VSV\GVQ_API\Team\Models\Team;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;

class TeamScoreNormalizerTest extends TestCase
{
    /**
     * @var TeamScoreNormalizer
     */
    private $teamScoreNormalizer;

    protected function setUp(): void
    {
        $this->teamScoreNormalizer = new TeamScoreNormalizer(
            new TeamNormalizer()
        );
    }

    /**
     * @test
     */
    public function it_can_normalize_to_json(): void
    {
        $teamScore = ModelsFactory::createLeuvenTeamScore();
        $expectedJson = ModelsFactory::createJson('team_score');
        $actualJson = $this->teamScoreNormalizer->normalize(
            $teamScore,
            'json'
        );
        $this->assertEquals(
            $expectedJson,
            json_encode($actualJson)
        );
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     * @param TeamScore|Team $data
     * @param string $format
     */
    public function it_only_supports_answer_type_and_json_format(
        $data,
        string $format
    ): void {
        $this->assertFalse(
            $this->teamScoreNormalizer->supportsNormalization(
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
                ModelsFactory::createLeuvenTeamScore(),
                'xml',
            ],
            [
                ModelsFactory::createLeuvenTeam(),
                'json',
            ],
        ];
    }
}
