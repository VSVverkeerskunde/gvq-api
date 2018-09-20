<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

interface QuestionDifficultyRepository
{
    /**
     * @param Question $question
     */
    public function increment(Question $question): void;

    /**
     * @param NaturalNumber $end
     * @return QuestionDifficulties
     */
    public function getRange(NaturalNumber $end): QuestionDifficulties;
}
