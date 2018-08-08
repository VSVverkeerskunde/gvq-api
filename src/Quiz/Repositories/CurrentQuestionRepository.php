<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;

interface CurrentQuestionRepository
{
    /**
     * @param UuidInterface $quizId
     * @param Question $question
     * @param array|null $context
     */
    public function save(UuidInterface $quizId, Question $question, ?array $context): void;

    /**
     * @param UuidInterface $quizId
     * @return Question
     */
    public function getById(UuidInterface $quizId): Question;

    /**
     * @param UuidInterface $quizId
     * @return string
     */
    public function getByIdAsJson(UuidInterface $quizId): string;
}
