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
        $teamScores = [];

        foreach ($teams->toArray() as $team) {
            $participationCount = $this->teamParticipationRepository->getCountForTeam($team);
            $totalScore = $this->teamTotalScoreRepository->getCountForTeam($team);
            $teamScore = new TeamScore(
                $team,
                new NaturalNumber($totalScore),
                new NaturalNumber($participationCount)
            );
            $teamScores[] = $teamScore;
        }

        usort(
            $teamScores,
            $this->sortByParticipationCount()
        );

        $iterator = 24;
        foreach ($teamScores as $key => $teamScore) {
            /** @var TeamScore $teamScore */
            $weightedParticipationScore = new Average(($iterator / 24) * 15 * 0.1);
            $teamScore = $teamScore->withWeightedParticipationScore($weightedParticipationScore);
            $teamScore->calculateWeightedTotalScore();
            $teamScores[$key] = $teamScore;

            $iterator--;
        }

        usort(
            $teamScores,
            $this->sortByWeightedTotalScore()
        );

        return new TeamScores(...$teamScores);
    }

    /**
     * @return \Closure
     */
    private function sortByParticipationCount(): \Closure
    {
        return function (TeamScore $ts1, TeamScore $ts2): int {
            if ($ts1->getParticipationCount()->toNative() > $ts2->getParticipationCount()->toNative()) {
                return -1;
            } elseif ($ts1->getParticipationCount()->toNative() < $ts2->getParticipationCount()->toNative()) {
                return 1;
            } else {
                return $this->sortAlphabetically($ts1, $ts2);
            }
        };
    }

    /**
     * @return \Closure
     */
    private function sortByWeightedTotalScore(): \Closure
    {
        return function (TeamScore $ts1, TeamScore $ts2): int {
            if ($ts1->getRankingScore()->toNative() > $ts2->getRankingScore()->toNative()) {
                return -1;
            } elseif ($ts1->getRankingScore()->toNative() < $ts2->getRankingScore()->toNative()) {
                return 1;
            } else {
                return $this->sortAlphabetically($ts1, $ts2);
            }
        };
    }

    /**
     * @param TeamScore $ts1
     * @param TeamScore $ts2
     * @return int
     */
    private function sortAlphabetically(TeamScore $ts1, TeamScore $ts2): int
    {
        return strcmp(
            $ts1->getTeam()->getName()->toNative(),
            $ts2->getTeam()->getName()->toNative()
        );
    }
}
