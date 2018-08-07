<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Repositories;

use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\ValueObjects\Year;

interface PartnerRepository
{
    /**
     * @param Year $year
     * @param Alias $alias
     * @return null|Partner
     */
    public function getByYearAndAlias(Year $year, Alias $alias): ?Partner;
}
