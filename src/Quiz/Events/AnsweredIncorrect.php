<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;

class AnsweredIncorrect extends AbstractAnsweredEvent
{
    /**
     * @var bool
     */
    private $answeredTooLate;

    /**
     * @param UuidInterface $id
     * @param Question $question
     * @param Answer $answer
     * @param \DateTimeImmutable $answeredOn
     * @param bool $answeredTooLate
     */
    public function __construct(
        UuidInterface $id,
        Question $question,
        Answer $answer,
        \DateTimeImmutable $answeredOn,
        bool $answeredTooLate
    ) {
        parent::__construct(
            $id,
            $question,
            $answer,
            $answeredOn
        );
        $this->answeredTooLate = $answeredTooLate;
    }

    /**
     * @return bool
     */
    public function isAnsweredTooLate(): bool
    {
        return $this->answeredTooLate;
    }
}
