<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class UniqueParticipantRedisRepository implements UniqueParticipantRepository
{
    const KEY_PREFIX = 'unique_participants_';

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @inheritdoc
     */
    public function add(StatisticsKey $statisticsKey, QuizParticipant $participant): void
    {
        $this->redis->sAdd($this->createKey($statisticsKey), $participant->getEmail()->toNative());
    }

    /**
     * @inheritdoc
     */
    public function addForPartner(StatisticsKey $statisticsKey, QuizParticipant $participant, Partner $partner): void
    {
        $this->redis->sAdd(
            $this->createPartnerKey($statisticsKey, $partner),
            $participant->getEmail()->toNative()
        );
    }

    /**
     * @inheritdoc
     */
    public function getCount(StatisticsKey $statisticsKey): int
    {
        return $this->redis->scard($this->createKey($statisticsKey));
    }

    /**
     * @inheritdoc
     */
    public function getCountForPartner(StatisticsKey $statisticsKey, Partner $partner): int
    {
        return $this->redis->scard($this->createPartnerKey($statisticsKey, $partner));
    }

    /**
     * @param StatisticsKey $statisticsKey
     * @return string
     */
    private function createKey(StatisticsKey $statisticsKey): string
    {
        return self::KEY_PREFIX.$statisticsKey->toNative();
    }

    /**
     * @param StatisticsKey $statisticsKey
     * @param Partner $partner
     * @return string
     */
    private function createPartnerKey(
        StatisticsKey $statisticsKey,
        Partner $partner
    ): string {
        return self::KEY_PREFIX.$partner->getId()->toString().'_'.
            $statisticsKey->getLanguage()->toNative();
    }
}
