<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

interface QuestionCounterRepository
{
    /**
     * @param Question $question
     */
    public function increment(Question $question): void;

    /**
     * @param Question $question
     * @return NaturalNumber
     */
    public function getCount(Question $question): NaturalNumber;
}
