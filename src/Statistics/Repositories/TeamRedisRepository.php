<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Team\Models\Team;

abstract class TeamRedisRepository extends AbstractRedisRepository implements TeamRepository
{
    /**
     * @inheritdoc
     */
    public function getForTeam(Team $team): int
    {
        return (int)$this->redis->get($this->createTeamKey($team));
    }

    /**
     * @param Team $team
     * @return string
     */
    abstract protected function createTeamKey(Team $team): string;
}
