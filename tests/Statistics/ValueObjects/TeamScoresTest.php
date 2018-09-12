<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class TeamScoresTest extends TestCase
{
    /**
     * @var TeamScore[]
     */
    private $teamScoresArray;

    /**
     * @var TeamScores
     */
    private $teamScores;

    protected function setUp(): void
    {
        $this->teamScoresArray = [
            ModelsFactory::createAntwerpTeamScore(),
            ModelsFactory::createLeuvenTeamScore(),
            ModelsFactory::createWaaslandTeamScore(),
            ModelsFactory::createTubizeTeamScore(),
            ModelsFactory::createLommelTeamScore(),
            ModelsFactory::createRoeselareTeamScore(),
        ];

        $this->teamScores = ModelsFactory::createTeamScores();
    }

    /**
     * @test
     */
    public function it_can_iterate_over_teamScores(): void
    {
        $actualTeamScoresArray = [];
        foreach ($this->teamScores as $teamScore) {
            $actualTeamScoresArray[] = $teamScore;
        }

        $this->assertEquals($this->teamScoresArray, $actualTeamScoresArray);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(6, count($this->teamScores));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->teamScoresArray,
            $this->teamScores->toArray()
        );
    }

    /**
     * @test
     */
    public function it_can_sort_by_participation_count(): void
    {
        $this->teamScores->sortByParticipationCount();

        $this->assertEquals(
            ModelsFactory::createLommelTeamScore(),
            $this->teamScores->toArray()[0]
        );
        $this->assertEquals(
            ModelsFactory::createAntwerpTeamScore(),
            $this->teamScores->toArray()[1]
        );
        $this->assertEquals(
            ModelsFactory::createLeuvenTeamScore(),
            $this->teamScores->toArray()[2]
        );
        $this->assertEquals(
            ModelsFactory::createRoeselareTeamScore(),
            $this->teamScores->toArray()[3]
        );
        $this->assertEquals(
            ModelsFactory::createTubizeTeamScore(),
            $this->teamScores->toArray()[4]
        );
        $this->assertEquals(
            ModelsFactory::createWaaslandTeamScore(),
            $this->teamScores->toArray()[5]
        );
    }

    /**
     * @test
     */
    public function it_can_sort_by_ranking_score(): void
    {
        $this->teamScores->sortByParticipationCount();

        $this->assertEquals(
            ModelsFactory::createLommelTeamScore(),
            $this->teamScores->toArray()[0]
        );
        $this->assertEquals(
            ModelsFactory::createAntwerpTeamScore(),
            $this->teamScores->toArray()[1]
        );
        $this->assertEquals(
            ModelsFactory::createLeuvenTeamScore(),
            $this->teamScores->toArray()[2]
        );
        $this->assertEquals(
            ModelsFactory::createRoeselareTeamScore(),
            $this->teamScores->toArray()[3]
        );
        $this->assertEquals(
            ModelsFactory::createTubizeTeamScore(),
            $this->teamScores->toArray()[4]
        );
        $this->assertEquals(
            ModelsFactory::createWaaslandTeamScore(),
            $this->teamScores->toArray()[5]
        );
    }

    /**
     * @test
     */
    public function it_sorts_alphabetically_when_ranking_score_is_equal(): void
    {
        $tubizeTeam = ModelsFactory::createTubizeTeam();
        $waaslandTeam = ModelsFactory::createWaaslandTeam();

        $teamScore1 = ModelsFactory::createCustomTeamScore(
            $waaslandTeam,
            new NaturalNumber(10),
            new NaturalNumber(10)
        );

        $teamScore2 = ModelsFactory::createCustomTeamScore(
            $tubizeTeam,
            new NaturalNumber(10),
            new NaturalNumber(10)
        );

        $teamScore1->calculateWeightedParticipationScore(0);
        $teamScore2->calculateWeightedParticipationScore(0);

        $teamScore1->calculateRankingScore();
        $teamScore2->calculateRankingScore();

        $this->assertEquals(
            $teamScore1->getRankingScore(),
            $teamScore2->getRankingScore()
        );

        $teamScores = new TeamScores($teamScore1, $teamScore2);

        $this->assertEquals(
            $tubizeTeam,
            $teamScores->toArray()[1]->getTeam()
        );

        $this->assertEquals(
            $waaslandTeam,
            $teamScores->toArray()[0]->getTeam()
        );

        $teamScores->sortByRankingScore();

        $this->assertEquals(
            $tubizeTeam,
            $teamScores->toArray()[0]->getTeam()
        );

        $this->assertEquals(
            $waaslandTeam,
            $teamScores->toArray()[1]->getTeam()
        );
    }
}
