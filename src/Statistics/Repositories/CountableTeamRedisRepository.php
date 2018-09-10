<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Team\Models\Team;

abstract class CountableTeamRedisRepository extends AbstractRedisRepository implements CountableTeamRepository
{
    /**
     * @inheritdoc
     */
    public function getCountForTeam(Team $team): int
    {
        return (int)$this->redis->get($this->createTeamKey($team));
    }

    /**
     * @param Team $team
     * @return string
     */
    abstract protected function createTeamKey(Team $team): string;
}
