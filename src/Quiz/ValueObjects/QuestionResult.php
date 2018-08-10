<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\Question\Models\Question;

class QuestionResult
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var bool|null
     */
    private $answeredTooLate;

    /**
     * @var int|null
     */
    private $score;

    /**
     * @param Question $question
     * @param bool|null $answeredTooLate
     * @param int|null $score
     */
    public function __construct(Question $question, ?bool $answeredTooLate, ?int $score)
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
     * @return bool|null
     */
    public function isAnsweredTooLate(): ?bool
    {
        return $this->answeredTooLate;
    }

    /**
     * @return int|null
     */
    public function getScore(): ?int
    {
        return $this->score;
    }
}
