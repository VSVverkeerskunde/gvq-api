<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

class PassedQuizRedisRepository extends IncrementableAndCountableRedisRepository implements PassedQuizRepository
{
    const KEY_PREFIX = 'passed_quizzes_';

    /**
     * @inheritdoc
     */
    public function getPrefix(): string
    {
        return self::KEY_PREFIX;
    }
}
