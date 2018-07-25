<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;

class QuestionAsked extends QuizEvent
{
    /**
     * @var Question
     */
    private $question;

    /**
     * @var \DateTimeImmutable
     */
    private $askedOn;

    /**
     * QuestionAsked constructor.
     * @param UuidInterface $id
     * @param Question $question
     * @param \DateTimeImmutable $askedOn
     */
    public function __construct(
        UuidInterface $id,
        Question $question,
        \DateTimeImmutable $askedOn
    ) {
        parent::__construct($id);

        $this->question = $question;
        $this->askedOn = $askedOn;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getAskedOn(): \DateTimeImmutable
    {
        return $this->askedOn;
    }
}
