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
    private $rankingScore;

    /**
     * @param Team $team
     * @param NaturalNumber $totalScore
     * @param NaturalNumber $participationCount
     * @param Average $rankingScore
     */
    public function __construct(
        Team $team,
        NaturalNumber $totalScore,
        NaturalNumber $participationCount,
        Average $rankingScore
    ) {
        $this->team = $team;
        $this->totalScore = $totalScore;
        $this->participationCount = $participationCount;
        $this->rankingScore = $rankingScore;
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
