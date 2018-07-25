<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Quiz\Models\Quiz;

class QuizStarted extends QuizEvent
{
    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @param UuidInterface $id
     * @param Quiz $quiz
     */
    public function __construct(
        UuidInterface $id,
        Quiz $quiz
    ) {
        parent::__construct($id);

        $this->quiz = $quiz;
    }

    /**
     * @return Quiz
     */
    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }
}
