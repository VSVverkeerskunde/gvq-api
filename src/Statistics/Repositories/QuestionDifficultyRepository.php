<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;

interface QuestionDifficultyRepository
{
    /**
     * @param Question $question
     */
    public function increment(Question $question): void;

    /**
     * @return QuestionDifficulties
     */
    public function getRange(): QuestionDifficulties;
}
