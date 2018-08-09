<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;

class QuizFinished extends AbstractQuizEvent
{
    /**
     * @var int
     */
    private $score;

    /**
     * @param int $score
     * @param UuidInterface $id
     */
    public function __construct(
        UuidInterface $id,
        int $score
    ) {
        parent::__construct($id);
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }
}
