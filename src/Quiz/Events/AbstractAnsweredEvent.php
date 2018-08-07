<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;

abstract class AbstractAnsweredEvent extends AbstractQuizEvent
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var Answer
     */
    private $answer;

    /**
     * @var \DateTimeImmutable
     */
    private $answeredOn;

    /**
     * @param UuidInterface $id
     * @param Question $question
     * @param Answer $answer
     * @param \DateTimeImmutable $answeredOn
     */
    public function __construct(
        UuidInterface $id,
        Question $question,
        Answer $answer,
        \DateTimeImmutable $answeredOn
    ) {
        parent::__construct($id);

        $this->question = $question;
        $this->answer = $answer;
        $this->answeredOn = $answeredOn;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return Answer
     */
    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getAnsweredOn(): \DateTimeImmutable
    {
        return $this->answeredOn;
    }
}
