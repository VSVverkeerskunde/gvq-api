<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;

interface EmployeeParticipationRepository
{
    public function save(EmployeeParticipation $employee): void;

    public function countByCompany(UuidInterface $companyId): int;
}
