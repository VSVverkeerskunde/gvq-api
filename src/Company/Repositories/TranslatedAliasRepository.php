<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\TranslatedAlias;

interface TranslatedAliasRepository
{
    public function save(TranslatedAlias $translatedAlias): void;

    public function getById(UuidInterface $id): ?TranslatedAlias;
}
