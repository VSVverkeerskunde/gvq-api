<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\ContestParticipations;

interface ContestParticipationRepository
{
    /**
     * @param ContestParticipation $contestParticipation
     */
    public function save(ContestParticipation $contestParticipation): void;

    /**
     * @return ContestParticipations|null
     */
    public function getAll(): ?ContestParticipations;
}
