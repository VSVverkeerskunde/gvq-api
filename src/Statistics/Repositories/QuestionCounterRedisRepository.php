<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class QuestionCounterRedisRepository extends AbstractRedisRepository implements QuestionCounterRepository
{
    /**
     * @var NotEmptyString
     */
    private $keyPrefix;

    /**
     * @param \Redis $redis
     * @param NotEmptyString $keyPrefix
     */
    public function __construct(
        \Redis $redis,
        NotEmptyString $keyPrefix
    ) {
        parent::__construct($redis);

        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @param Question $question
     */
    public function increment(Question $question): void
    {
        $this->redis->incr($this->createKey($question));
    }

    /**
     * @param Question $question
     * @return NaturalNumber
     */
    public function getCount(Question $question): NaturalNumber
    {
        $count = $this->redis->get($this->createKey($question));

        return new NaturalNumber((int)$count);
    }

    /**
     * @param Question $question
     * @return string
     */
    private function createKey(Question $question): string
    {
        return $this->keyPrefix->toNative().'_'.$question->getId()->toString();
    }
}
