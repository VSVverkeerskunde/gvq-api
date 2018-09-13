<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Team\Models\Team;

class TeamParticipationRedisRepository extends TeamRedisRepository implements TeamParticipationRepository
{
    const KEY_PREFIX = 'team_participations_';

    /**
     * @inheritdoc
     */
    public function incrementCountForTeam(Team $team): void
    {
        $this->redis->incr($this->createTeamKey($team));
    }

    /**
     * @inheritdoc
     */
    public function createTeamKey(Team $team): string
    {
        return self::KEY_PREFIX.$team->getId()->toString();
    }
}
