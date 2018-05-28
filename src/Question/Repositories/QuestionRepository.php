<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;

interface QuestionRepository
{
    /**
     * @param Question $question
     */
    public function save(Question $question): void;

    /**
     * @param Question $question
     */
    public function update(Question $question): void;

    /**
     * @param UuidInterface $id
     */
    public function delete(UuidInterface $id): void;

    /**
     * @param UuidInterface $id
     * @return null|Question
     */
    public function getById(UuidInterface $id): ?Question;

    /**
     * @return null|Questions
     */
    public function getAll(): ?Questions;
}
