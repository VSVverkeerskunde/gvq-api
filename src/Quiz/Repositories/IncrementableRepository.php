<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

interface IncrementableRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     */
    public function incrementCount(StatisticsKey $statisticsKey): void;
}
