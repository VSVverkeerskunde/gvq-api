<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

interface CountableRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     * @return int
     */
    public function getCount(StatisticsKey $statisticsKey): int;
}
