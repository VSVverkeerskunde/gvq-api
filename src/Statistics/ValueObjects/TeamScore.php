<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use VSV\GVQ_API\Team\Models\Team;

class TeamScore
{
    /**
     * @var Team
     */
    private $team;

    /**
     * @var NaturalNumber
     */
    private $totalScore;

    /**
     * @var NaturalNumber
     */
    private $participationCount;

    /**
     * @var Average
     */
    private $weightedAverageScore;

    /**
     * @var Average
     */
    private $weightedParticipationScore;

    /**
     * @var Average
     */
    private $rankingScore;

    /**
     * @param Team $team
     * @param NaturalNumber $totalScore
     * @param NaturalNumber $participationCount
     */
    public function __construct(
        Team $team,
        NaturalNumber $totalScore,
        NaturalNumber $participationCount
    ) {
        $this->team = $team;
        $this->totalScore = $totalScore;
        $this->participationCount = $participationCount;

        $this->calculateWeightedAverageScore();

        $this->weightedParticipationScore = new Average(0);
        $this->rankingScore = new Average(0);
    }

    /**
     * @param int $position
     */
    public function calculateWeightedParticipationScore(int $position): void
    {
        $this->weightedParticipationScore = new Average(($position / 24) * 15 * 0.1);
    }

    public function calculateRankingScore(): void
    {
        $this->rankingScore = new Average(
            $this->weightedParticipationScore->toNative() +
            $this->weightedAverageScore->toNative()
        );
    }

    private function calculateWeightedAverageScore(): void
    {
        if ($this->participationCount->toNative() === 0) {
            $this->weightedAverageScore = new Average(0);
        } else {
            $this->weightedAverageScore = new Average(
                ($this->totalScore->toNative() / $this->participationCount->toNative()) * 0.9
            );
        }
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @return NaturalNumber
     */
    public function getTotalScore(): NaturalNumber
    {
        return $this->totalScore;
    }

    /**
     * @return NaturalNumber
     */
    public function getParticipationCount(): NaturalNumber
    {
        return $this->participationCount;
    }

    /**
     * @return Average
     */
    public function getRankingScore(): Average
    {
        return $this->rankingScore;
    }
}
