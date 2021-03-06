<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

interface QuestionResultRepository
{
    /**
     * @param UuidInterface $quizId
     * @param QuestionResult $questionResult
     * @param array $context
     */
    public function save(
        UuidInterface $quizId,
        QuestionResult $questionResult,
        array $context = []
    ): void;

    /**
     * @param UuidInterface $id
     */
    public function deleteById(UuidInterface $id): void;

    /**
     * @param UuidInterface $quizId
     * @return QuestionResult
     */
    public function getById(UuidInterface $quizId): QuestionResult;

    /**
     * @param UuidInterface $quizId
     * @return string
     */
    public function getByIdAsJson(UuidInterface $quizId): string;
}
