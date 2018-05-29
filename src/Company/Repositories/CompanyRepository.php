<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\Company;

interface CompanyRepository
{
    public function save(Company $company): void;

    public function getById(UuidInterface $id): ?Company;
}
