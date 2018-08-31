<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

interface UniqueParticipantRepository extends CountableRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     * @param QuizParticipant $participant
     */
    public function add(StatisticsKey $statisticsKey, QuizParticipant $participant): void;

    /**
     * @param StatisticsKey $statisticsKey
     * @param QuizParticipant $participant
     * @param Partner $partner
     */
    public function addForPartner(StatisticsKey $statisticsKey, QuizParticipant $participant, Partner $partner): void;

    /**
     * @param StatisticsKey $statisticsKey
     * @param Partner $partner
     * @return int
     */
    public function getCountForPartner(StatisticsKey $statisticsKey, Partner $partner): int;
}
