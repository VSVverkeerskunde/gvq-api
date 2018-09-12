<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Service;

use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;
use VSV\GVQ_API\Statistics\Serializers\TeamScoresNormalizer;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
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
     * @var TeamScoresNormalizer
     */
    private $teamScoresNormalizer;

    /**
     * @param Year $year
     * @param TeamRepository $teamRepository
     * @param TeamTotalScoreRepository $teamTotalScoreRepository
     * @param TeamParticipationRepository $teamParticipationRepository
     * @param TeamScoresNormalizer $teamScoresNormalizer
     */
    public function __construct(
        Year $year,
        TeamRepository $teamRepository,
        TeamTotalScoreRepository $teamTotalScoreRepository,
        TeamParticipationRepository $teamParticipationRepository,
        TeamScoresNormalizer $teamScoresNormalizer
    ) {
        $this->year = $year;
        $this->teamRepository = $teamRepository;
        $this->teamTotalScoreRepository = $teamTotalScoreRepository;
        $this->teamParticipationRepository = $teamParticipationRepository;
        $this->teamScoresNormalizer = $teamScoresNormalizer;
    }

    /**
     * @return string
     */
    public function getTeamRankingAsJson(): string
    {
        $teams = $this->teamRepository->getAllByYear($this->year);

        if ($teams === null) {
            return '';
        }

        $rankedTeamScores = $this->rankTeamScores($teams);

        $teamRankingAsJson = $this->teamScoresNormalizer->normalize($rankedTeamScores);

        return json_encode($teamRankingAsJson);
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
