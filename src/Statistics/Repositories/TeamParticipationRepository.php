<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Team\Models\Team;

interface TeamParticipationRepository extends TeamRepository
{
    /**
     * @param Team $team
     */
    public function incrementCountForTeam(Team $team): void;
}
