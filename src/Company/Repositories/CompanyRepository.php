<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\User\Models\User;

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
     * @param NotEmptyString $name
     * @return null|Company
     */
    public function getByName(NotEmptyString $name): ?Company;

    /**
     * @param Alias $alias
     * @return null|Company
     */
    public function getByAlias(Alias $alias): ?Company;

    /**
     * @return null|Companies
     */
    public function getAll(): ?Companies;

    /**
     * @param User $user
     * @return null|Companies
     */
    public function getAllByUser(User $user): ?Companies;
}
