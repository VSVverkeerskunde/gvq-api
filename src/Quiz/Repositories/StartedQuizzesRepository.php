<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

interface StartedQuizzesRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     * @return int
     */
    public function getTotal(StatisticsKey $statisticsKey): int;

    /**
     * @param StatisticsKey $statisticsKey
     */
    public function incrementTotal(StatisticsKey $statisticsKey): void;
}
