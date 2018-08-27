<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

interface UniqueParticipantRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     * @param QuizParticipant $participant
     */
    public function add(StatisticsKey $statisticsKey, QuizParticipant $participant): void;

    /**
     * @param StatisticsKey $statisticsKey
     * @return int
     */
    public function getCount(StatisticsKey $statisticsKey): int;
}
