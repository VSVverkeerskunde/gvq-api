<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\User\ValueObjects\Email;

interface UniqueParticipantRepository
{
    /**
     * @param StatisticsKey $statisticsKey
     * @param Email $email
     */
    public function add(StatisticsKey $statisticsKey, Email $email): void;

    /**
     * @param StatisticsKey $statisticsKey
     * @return int
     */
    public function getCount(StatisticsKey $statisticsKey): int;
}
