<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
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

    /**
     * @param QuizChannel $channel
     * @return Average
     */
    public function getAverageByChannel(QuizChannel $channel): Average;

    /**
     * @param Language $language
     * @return Average
     */
    public function getAverageByLanguage(Language $language): Average;

    /**
     * @param Language|null $language
     * @return Average
     */
    public function getQuizAverage(?Language $language): Average;

    /**
     * @return Average
     */
    public function getTotalAverage(): Average;
}
