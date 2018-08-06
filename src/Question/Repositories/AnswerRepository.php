<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Answer;

interface AnswerRepository
{
    /**
     * @param UuidInterface $id
     * @return null|Answer
     */
    public function getById(UuidInterface $id): ?Answer;
}
