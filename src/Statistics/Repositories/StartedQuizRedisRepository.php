<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

class StartedQuizRedisRepository extends IncrementableAndCountableRedisRepository implements StartedQuizRepository
{
    const KEY_PREFIX = 'started_quizzes_';

    /**
     * @inheritdoc
     */
    public function getPrefix(): string
    {
        return self::KEY_PREFIX;
    }
}
