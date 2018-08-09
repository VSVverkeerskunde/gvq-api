<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

abstract class AbstractAnsweredEvent extends AbstractQuizEvent
{
    /**
     * @var QuestionResult
     */
    private $questionResult;

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
     * @param QuestionResult $questionResult
     * @param Answer $answer
     * @param \DateTimeImmutable $answeredOn
     */
    public function __construct(
        UuidInterface $id,
        QuestionResult $questionResult,
        Answer $answer,
        \DateTimeImmutable $answeredOn
    ) {
        parent::__construct($id);

        $this->questionResult = $questionResult;
        $this->answer = $answer;
        $this->answeredOn = $answeredOn;
    }

    /**
     * @return QuestionResult
     */
    public function getQuestionResult(): QuestionResult
    {
        return $this->questionResult;
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
