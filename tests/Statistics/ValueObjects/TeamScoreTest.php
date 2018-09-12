<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class TeamScoreTest extends TestCase
{
    /**
     * @var TeamScore
     */
    private $teamScore;

    protected function setUp(): void
    {
        $this->teamScore = ModelsFactory::createLeuvenTeamScore();
    }

    /**
     * @test
     */
    public function it_can_store_a_team(): void
    {
        $this->assertEquals(
            ModelsFactory::createLeuvenTeam(),
            $this->teamScore->getTeam()
        );
    }

    /**
     * @test
     */
    public function it_can_store_total_score(): void
    {
        $this->assertEquals(
            16,
            $this->teamScore->getTotalScore()->toNative()
        );
    }

    /**
     * @test
     */
    public function it_can_store_participation_count(): void
    {
        $this->assertEquals(
            2,
            $this->teamScore->getParticipationCount()->toNative()
        );
    }

    /**
     * @test
     */
    public function it_can_calculate_ranking_score(): void
    {
        $this->teamScore->calculateWeightedParticipationScore(23);
        $this->teamScore->calculateRankingScore();

        $this->assertEquals(
            8.6375,
            $this->teamScore->getRankingScore()->toNative()
        );
    }
}
