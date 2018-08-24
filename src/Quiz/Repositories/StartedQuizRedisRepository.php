<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

class StartedQuizRedisRepository extends AbstractCounterRedisRepository
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
