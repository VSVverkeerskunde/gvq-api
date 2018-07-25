<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;

class QuizAggregate extends EventSourcedAggregateRoot
{
    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @var int
     */
    private $questionIndex;

    /**
     * @var \DateTimeImmutable
     */
    private $questionAskedOn;

    /**
     * @inheritdoc
     */
    public function getAggregateRootId(): string
    {
        return $this->quiz->getId()->toString();
    }

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
        $this->questionIndex = 0;
    }

    /**
     * @param \DateTimeImmutable $askedOn
     */
    public function askQuestion(\DateTimeImmutable $askedOn)
    {
        // TODO: What if index out of bounds?
        // TODO: What if question never gets answered?
        // TODO: What if askQuestion called but previous question not answered?
        $questions = $this->quiz->getQuestions();
        $question = $questions->toArray()[$this->questionIndex];

        $this->apply(
            new QuestionAsked(
                $this->quiz->getId(),
                $question,
                $askedOn
            )
        );
    }

    /**
     * @param QuestionAsked $questionAsked
     */
    protected function applyQuestionAsked(QuestionAsked $questionAsked)
    {
        $this->questionAskedOn = $questionAsked->getAskedOn();
    }
}
