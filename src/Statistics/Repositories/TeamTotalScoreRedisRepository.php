<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Team\Models\Team;

class TeamTotalScoreRedisRepository extends TeamRedisRepository implements TeamTotalScoreRepository
{
    const KEY_PREFIX = 'team_total_score_';

    /**
     * @inheritdoc
     */
    public function incrementTotalScoreByQuizScore(Team $team, int $quizScore): void
    {
        $this->redis->incrBy($this->createTeamKey($team), $quizScore);
    }

    /**
     * @inheritdoc
     */
    public function createTeamKey(Team $team): string
    {
        return self::KEY_PREFIX.$team->getId()->toString();
    }
}
