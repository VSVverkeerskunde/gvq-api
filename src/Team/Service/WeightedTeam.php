<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Service;

use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\Team\Models\Team;

class WeightedTeam
{
    /**
     * @var Team
     */
    private $team;

    /**
     * @var NaturalNumber
     */
    private $participationCount;

    /**
     * @var NaturalNumber
     */
    private $totalScore;

    /**
     * @var Average
     */
    private $weightedAverageScore;

    /**
     * @param Team $team
     * @param NaturalNumber $participationCount
     * @param NaturalNumber $totalScore
     * @param Average $weightedAverageScore
     */
    public function __construct(
        Team $team,
        NaturalNumber $participationCount,
        NaturalNumber $totalScore,
        Average $weightedAverageScore
    ) {
        $this->team = $team;
        $this->participationCount = $participationCount;
        $this->totalScore = $totalScore;
        $this->weightedAverageScore = $weightedAverageScore;
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
    public function getParticipationCount(): NaturalNumber
    {
        return $this->participationCount;
    }

    /**
     * @return NaturalNumber
     */
    public function getTotalScore(): NaturalNumber
    {
        return $this->totalScore;
    }

    /**
     * @return Average
     */
    public function getWeightedAverageScore(): Average
    {
        return $this->weightedAverageScore;
    }
}
