<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\ValueObjects\Alias;

interface CompanyRepository
{
    public function save(Company $company): void;

    public function getById(UuidInterface $id): ?Company;

    public function getByName(NotEmptyString $name): ?Company;

    public function getByAlias(Alias $alias): ?Company;
}
