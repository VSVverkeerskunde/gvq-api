<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Company\Models\Company;

interface CompanyRepository
{
    /**
     * @param Company $company
     */
    public function save(Company $company): void;

    /**
     * @param Company $company
     */
    public function update(Company $company): void;

    /**
     * @param UuidInterface $id
     * @return null|Company
     */
    public function getById(UuidInterface $id): ?Company;

    /**
     * @return null|Companies
     */
    public function getAll(): ?Companies;
}
