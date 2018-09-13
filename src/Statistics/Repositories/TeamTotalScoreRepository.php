<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Team\Models\Team;

interface TeamTotalScoreRepository extends TeamRepository
{
    /**
     * @param Team $team
     * @param int $quizScore
     */
    public function incrementTotalScoreByQuizScore(Team $team, int $quizScore): void;
}
