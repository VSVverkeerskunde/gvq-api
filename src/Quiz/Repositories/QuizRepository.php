<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Quiz\Models\Quiz;

interface QuizRepository
{
    /**
     * @param Quiz $quiz
     */
    public function save(Quiz $quiz): void;

    /**
     * @param UuidInterface $id
     */
    public function deleteById(UuidInterface $id): void;

    /**
     * @param UuidInterface $id
     * @return Quiz
     */
    public function getById(UuidInterface $id): Quiz;
}
