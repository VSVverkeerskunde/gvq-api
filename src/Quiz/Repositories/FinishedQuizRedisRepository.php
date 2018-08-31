<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

class FinishedQuizRedisRepository extends IncrementableAndCountableRedisRepository implements FinishedQuizRepository
{
    const KEY_PREFIX = 'finished_quizzes_';

    /**
     * @inheritdoc
     */
    public function getPrefix(): string
    {
        return self::KEY_PREFIX;
    }
}
