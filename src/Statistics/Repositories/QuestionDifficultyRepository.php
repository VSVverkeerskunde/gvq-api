<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

interface QuestionDifficultyRepository
{
    /**
     * @param Question $question
     */
    public function update(Question $question): void;

    /**
     * @param Language $language
     * @param NaturalNumber $end
     * @return QuestionDifficulties
     */
    public function getBestRange(
        Language $language,
        NaturalNumber $end
    ): QuestionDifficulties;

    /**
     * @param Language $language
     * @param NaturalNumber $end
     * @return QuestionDifficulties
     */
    public function getWorstRange(
        Language $language,
        NaturalNumber $end
    ): QuestionDifficulties;
}
