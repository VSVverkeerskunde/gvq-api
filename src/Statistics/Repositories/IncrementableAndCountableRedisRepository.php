<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

// @codingStandardsIgnoreStart
abstract class IncrementableAndCountableRedisRepository extends AbstractRedisRepository implements CountableRepository, IncrementableRepository
// @codingStandardsIgnoreEnd
{
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
        return $this->getPrefix().$statisticsKey->toNative();
    }

    /**
     * @return string
     */
    abstract protected function getPrefix(): string;
}
