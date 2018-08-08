<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;

class QuestionAsked extends AbstractQuizEvent
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
     * @var array
     */
    private $context;

    /**
     * QuestionAsked constructor.
     * @param UuidInterface $id
     * @param Question $question
     * @param \DateTimeImmutable $askedOn
     * @param array $context
     */
    public function __construct(
        UuidInterface $id,
        Question $question,
        \DateTimeImmutable $askedOn,
        array $context
    ) {
        parent::__construct($id);

        $this->question = $question;
        $this->askedOn = $askedOn;
        $this->context = $context;
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

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
