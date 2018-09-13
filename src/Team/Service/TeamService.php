<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Service;

use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;
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
        /** @var WeightedTeam[] $weightedTeams */
        $weightedTeams = [];

        foreach ($teams as $team) {
            $participationCount = $this->teamParticipationRepository->getForTeam($team);
            $totalScore = $this->teamTotalScoreRepository->getForTeam($team);

            $weightedAverageScore = !empty($participationCount) ?
                new Average(($totalScore / $participationCount) * 0.9) : new Average(0);

            $weightedTeams[] = new WeightedTeam(
                $team,
                new NaturalNumber($participationCount),
                new NaturalNumber($totalScore),
                $weightedAverageScore
            );
        }

        /** @var TeamScore[] $teamScores */
        $teamScores = [];

        $weightedTeams = $this->sortByParticipationCount($weightedTeams);

        for ($index = count($weightedTeams); $index > 0; $index--) {
            $weightedTeam = $weightedTeams[count($weightedTeams) - $index];

            $weightedPositionScore = new Average(($index / 24) * 15 * 0.1);

            $rankingScore = new Average(
                $weightedTeam->getWeightedAverageScore()->toNative() + $weightedPositionScore->toNative()
            );

            $teamScores[] = new TeamScore(
                $weightedTeam->getTeam(),
                $weightedTeam->getTotalScore(),
                $weightedTeam->getParticipationCount(),
                $rankingScore
            );
        }

        return new TeamScores(...$teamScores);
    }

    /**
     * @param WeightedTeam[] $weightedTeams
     * @return WeightedTeam[]
     */
    public function sortByParticipationCount(array $weightedTeams): array
    {
        usort(
            $weightedTeams,
            function (WeightedTeam $wt1, WeightedTeam $wt2): int {
                if ($wt1->getParticipationCount()->toNative() > $wt2->getParticipationCount()->toNative()) {
                    return -1;
                } elseif ($wt1->getParticipationCount()->toNative() < $wt2->getParticipationCount()->toNative()) {
                    return 1;
                } else {
                    return strcmp(
                        $wt1->getTeam()->getName()->toNative(),
                        $wt2->getTeam()->getName()->toNative()
                    );
                }
            }
        );

        return $weightedTeams;
    }
}
