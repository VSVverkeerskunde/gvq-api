<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Repositories;

use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\ValueObjects\Year;

interface PartnerRepository
{
    public function getByAliasandYear(Alias $alias, Year $year): ?Partner;
}
