<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Question;

class QuestionResult
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var bool
     */
    private $answeredTooLate;

    /**
     * @var PositiveNumber
     */
    private $score;

    /**
     * @param Question $question
     * @param bool $answeredTooLate
     * @param PositiveNumber $score
     */
    public function __construct(Question $question, bool $answeredTooLate, PositiveNumber $score)
    {
        $this->question = $question;
        $this->answeredTooLate = $answeredTooLate;
        $this->score = $score;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return bool
     */
    public function isAnsweredTooLate(): bool
    {
        return $this->answeredTooLate;
    }

    /**
     * @return PositiveNumber
     */
    public function getScore(): PositiveNumber
    {
        return $this->score;
    }
}
