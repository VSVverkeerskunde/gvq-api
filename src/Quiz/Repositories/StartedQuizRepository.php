<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

interface StartedQuizRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     * @return int
     */
    public function getCount(StatisticsKey $statisticsKey): int;

    /**
     * @param StatisticsKey $statisticsKey
     */
    public function incrementCount(StatisticsKey $statisticsKey): void;
}
