<?php

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;

interface QuestionRepository
{
    public function save(Question $question): void;

    public function update(Question $question): void;

    public function delete(Question $question): void;

    public function getById(UuidInterface $id): ?Question;

    public function getAll(): ?Questions;
}
