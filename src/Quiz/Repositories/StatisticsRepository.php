<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

interface StatisticsRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     * @return int
     */
    public function getCount(StatisticsKey $statisticsKey): int;
}
