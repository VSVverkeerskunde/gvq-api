<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Statistics\Models\TeamParticipant;

interface TeamParticipantRepository
{
    /**
     * @param TeamParticipant $participant
     */
    public function save(TeamParticipant $participant): void;

    public function getParticipantCount(UuidInterface $teamId): int;
}
