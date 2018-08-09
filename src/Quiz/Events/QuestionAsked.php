<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionAsked extends AbstractQuizEvent
{
    /**
     * @var QuestionResult
     */
    private $questionResult;

    /**
     * @var \DateTimeImmutable
     */
    private $askedOn;

    /**
     * QuestionAsked constructor.
     * @param UuidInterface $id
     * @param QuestionResult $questionResultResult
     * @param \DateTimeImmutable $askedOn
     */
    public function __construct(
        UuidInterface $id,
        QuestionResult $questionResultResult,
        \DateTimeImmutable $askedOn
    ) {
        parent::__construct($id);

        $this->questionResult = $questionResultResult;
        $this->askedOn = $askedOn;
    }

    /**
     * @return QuestionResult
     */
    public function getQuestionResult(): QuestionResult
    {
        return $this->questionResult;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getAskedOn(): \DateTimeImmutable
    {
        return $this->askedOn;
    }
}
