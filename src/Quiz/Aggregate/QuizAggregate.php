<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;

class QuizAggregate extends EventSourcedAggregateRoot
{
    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @param Quiz $quiz
     * @return QuizAggregate
     */
    public static function start(Quiz $quiz): QuizAggregate
    {
        $quizAggregate = new self();

        $quizAggregate->apply(new QuizStarted($quiz->getId(), $quiz));

        return $quizAggregate;
    }

    /**
     * @param QuizStarted $quizStarted
     */
    protected function applyQuizStarted(QuizStarted $quizStarted)
    {
        $this->quiz = $quizStarted->getQuiz();
    }

    /**
     * @inheritdoc
     */
    public function getAggregateRootId(): string
    {
        return $this->quiz->getId()->toString();
    }
}
