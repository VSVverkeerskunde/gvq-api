<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Contest\Models\TieBreakers;

interface TieBreakerRepository
{
    public function getAllByYear(int $year): ?TieBreakers;
}
