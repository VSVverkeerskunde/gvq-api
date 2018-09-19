<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class QuestionDifficulty
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var NaturalNumber
     */
    private $count;

    /**
     * @param Question $question
     * @param NaturalNumber $count
     */
    public function __construct(Question $question, NaturalNumber $count)
    {
        $this->question = $question;
        $this->count = $count;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return NaturalNumber
     */
    public function getCount(): NaturalNumber
    {
        return $this->count;
    }
}
