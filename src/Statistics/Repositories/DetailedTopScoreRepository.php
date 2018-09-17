<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\Models\DetailedTopScore;
use VSV\GVQ_API\Statistics\ValueObjects\Average;

interface DetailedTopScoreRepository
{
    /**
     * @param DetailedTopScore $detailedTopScore
     */
    public function saveWhenHigher(DetailedTopScore $detailedTopScore): void;

    /**
     * @param StatisticsKey $statisticsKey
     * @return Average
     */
    public function getAverageByKey(StatisticsKey $statisticsKey): Average;
}
