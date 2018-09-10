<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Team\Models\Team;

interface CountableTeamRepository
{
    /**
     * @param Team $team
     * @return int
     */
    public function getCountForTeam(Team $team): int;
}
