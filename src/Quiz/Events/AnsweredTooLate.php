<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;

class AnsweredTooLate extends AbstractQuizEvent
{

    /**
     * @var Question
     */
    private $question;

    /**
     * @var \DateTimeImmutable
     */
    private $answeredOn;

    /**
     * @param UuidInterface $id
     * @param Question $question
     * @param \DateTimeImmutable $answeredOn
     */
    public function __construct(
        UuidInterface $id,
        Question $question,
        \DateTimeImmutable $answeredOn
    ) {
        parent::__construct(
            $id
        );
        $this->question = $question;
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
     * @return \DateTimeImmutable
     */
    public function getAnsweredOn(): \DateTimeImmutable
    {
        return $this->answeredOn;
    }
}
