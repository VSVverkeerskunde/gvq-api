<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Contest\Models\TieBreakers;
use VSV\GVQ_API\Question\ValueObjects\Year;

interface TieBreakerRepository
{
    /**
     * @param Year $year
     * @return null|TieBreakers
     */
    public function getAllByYear(Year $year): ?TieBreakers;
}
