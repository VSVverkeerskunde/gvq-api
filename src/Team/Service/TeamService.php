<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Service;

use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScore;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScores;
use VSV\GVQ_API\Team\Models\Teams;
use VSV\GVQ_API\Team\Repositories\TeamRepository;

class TeamService
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * @var TeamTotalScoreRepository
     */
    private $teamTotalScoreRepository;

    /**
     * @var TeamParticipationRepository
     */
    private $teamParticipationRepository;

    /**
     * @param Year $year
     * @param TeamRepository $teamRepository
     * @param TeamTotalScoreRepository $teamTotalScoreRepository
     * @param TeamParticipationRepository $teamParticipationRepository
     */
    public function __construct(
        Year $year,
        TeamRepository $teamRepository,
        TeamTotalScoreRepository $teamTotalScoreRepository,
        TeamParticipationRepository $teamParticipationRepository
    ) {
        $this->year = $year;
        $this->teamRepository = $teamRepository;
        $this->teamTotalScoreRepository = $teamTotalScoreRepository;
        $this->teamParticipationRepository = $teamParticipationRepository;
    }

    /**
     * @return TeamScores|null
     */
    public function getRankedTeamScores(): ?TeamScores
    {
        $teams = $this->teamRepository->getAllByYear($this->year);

        if ($teams === null) {
            return null;
        }

        $rankedTeamScores = $this->rankTeamScores($teams);

        return $rankedTeamScores;
    }

    /**
     * @param Teams $teams
     * @return TeamScores
     */
    private function rankTeamScores(Teams $teams): TeamScores
    {
        $teamScoresArray = [];

        foreach ($teams->toArray() as $team) {
            $participationCount = $this->teamParticipationRepository->getCountForTeam($team);
            $totalScore = $this->teamTotalScoreRepository->getCountForTeam($team);
            $teamScore = new TeamScore(
                $team,
                new NaturalNumber($totalScore),
                new NaturalNumber($participationCount)
            );
            $teamScoresArray[] = $teamScore;
        }

        $teamScores = new TeamScores(...$teamScoresArray);
        $teamScores = $teamScores->sortByParticipationCount();

        $positionIterator = 24;

        foreach ($teamScores->toArray() as $teamScore) {
            $teamScore->calculateWeightedParticipationScore($positionIterator);
            $teamScore->calculateRankingScore();
            $positionIterator--;
        }

        $teamScores = $teamScores->sortByRankingScore();

        return $teamScores;
    }
}
