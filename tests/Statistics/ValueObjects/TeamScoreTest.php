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
            new NaturalNumber(16),
            $this->teamScore->getTotalScore()
        );
    }

    /**
     * @test
     */
    public function it_can_store_participation_count(): void
    {
        $this->assertEquals(
            new NaturalNumber(2),
            $this->teamScore->getParticipationCount()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_ranking_score(): void
    {
        $this->assertEquals(
            new Average(8.575),
            $this->teamScore->getRankingScore()
        );
    }
}
