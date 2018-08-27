<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

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
    public function getCount(StatisticsKey $statisticsKey): int
    {
        return $this->redis->sCard($this->createKey($statisticsKey));
    }

    /**
     * @param StatisticsKey $statisticsKey
     * @return string
     */
    private function createKey(StatisticsKey $statisticsKey): string
    {
        return self::KEY_PREFIX.$statisticsKey->toNative();
    }
}
