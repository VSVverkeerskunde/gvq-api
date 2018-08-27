<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\User\ValueObjects\Email;

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

    /**
     * @param Year $year
     * @param Email $email
     * @return null|ContestParticipations
     */
    public function getAllByYearAndEmail(Year $year, Email $email): ?ContestParticipations;
}
