<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StartedQuizRedisRepository implements StartedQuizRepository
{
    const KEY_PREFIX = 'started_quizzes_';

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
    public function getCount(StatisticsKey $statisticsKey): int
    {
        return (int)$this->redis->get($this->createKey($statisticsKey));
    }

    /**
     * @inheritdoc
     */
    public function incrementCount(StatisticsKey $statisticsKey): void
    {
        $this->redis->incr($this->createKey($statisticsKey));
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
