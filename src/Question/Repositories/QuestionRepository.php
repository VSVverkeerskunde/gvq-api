<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;

interface QuestionRepository
{
    public function save(Question $question): void;

    public function getById(UuidInterface $id): ?Question;
}
