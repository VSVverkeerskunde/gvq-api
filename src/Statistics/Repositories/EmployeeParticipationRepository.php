<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

interface EmployeeParticipationRepository
{
    /**
     * @param EmployeeParticipation $employee
     */
    public function save(EmployeeParticipation $employee): void;

    /**
     * @param UuidInterface $companyId
     * @return NaturalNumber
     */
    public function countByCompany(UuidInterface $companyId): NaturalNumber;
}
