<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Statistics\ValueObjects\Percentage;

class QuestionDifficulty
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var Percentage
     */
    private $percentage;

    /**
     * @param Question $question
     * @param Percentage $percentage
     */
    public function __construct(Question $question, Percentage $percentage)
    {
        $this->question = $question;
        $this->percentage = $percentage;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return Percentage
     */
    public function getPercentage(): Percentage
    {
        return $this->percentage;
    }
}
