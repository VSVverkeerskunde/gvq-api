<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\AllowedDelay;

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
    protected function applyQuizStarted(QuizStarted $quizStarted): void
    {
        $this->quiz = $quizStarted->getQuiz();

        $this->questionIndex = 0;
    }

    /**
     * @param \DateTimeImmutable $askedOn
     */
    public function askQuestion(\DateTimeImmutable $askedOn): void
    {
        $this->apply(
            new QuestionAsked(
                $this->quiz->getId(),
                $this->getCurrentQuestion(),
                $askedOn
            )
        );
    }

    /**
     * @param QuestionAsked $questionAsked
     */
    protected function applyQuestionAsked(QuestionAsked $questionAsked): void
    {
        $this->questionAskedOn = $questionAsked->getAskedOn();
    }

    /**
     * @param \DateTimeImmutable $answeredOn
     * @param Answer $answer
     */
    public function answerQuestion(
        \DateTimeImmutable $answeredOn,
        Answer $answer
    ) {
        $currentQuestion = $this->getCurrentQuestion();

        if ($this->answeredToLate($this->questionAskedOn, $answeredOn, $this->quiz->getAllowedDelay()) ||
            !$this->answeredCorrect($currentQuestion->getAnswers(), $answer)) {
            $this->apply(
                new AnsweredIncorrect(
                    $this->quiz->getId(),
                    $currentQuestion,
                    $answer,
                    $answeredOn
                )
            );
        } else {
            $this->apply(
                new AnsweredCorrect(
                    $this->quiz->getId(),
                    $currentQuestion,
                    $answer,
                    $answeredOn
                )
            );
        }

        if (count($this->quiz->getQuestions()) === $this->questionIndex) {
            $this->apply(
                new QuizFinished(
                    $this->quiz->getId()
                )
            );
        }
    }

    protected function applyAnsweredIncorrect(): void
    {
        $this->questionIndex++;
    }

    protected function applyAnsweredCorrect(): void
    {
        $this->questionIndex++;
    }

    protected function applyQuizFinished(): void
    {
        $this->questionIndex = -1;
    }

    /**
     * @param \DateTimeImmutable $askedOn
     * @param \DateTimeImmutable $answeredOn
     * @param AllowedDelay $allowedDelay
     * @return bool
     */
    private function answeredToLate(
        \DateTimeImmutable $askedOn,
        \DateTimeImmutable $answeredOn,
        AllowedDelay $allowedDelay
    ): bool {
        $answerDelay = $answeredOn->getTimestamp() - $askedOn->getTimestamp();
        return ($answerDelay > $allowedDelay->toNative());
    }

    /**
     * @param Answers $answers
     * @param Answer $answer
     * @return bool
     */
    private function answeredCorrect(
        Answers $answers,
        Answer $answer
    ): bool {
        return $answers->getCorrectAnswer()->getId()->equals($answer->getId());
    }

    /**
     * @return Question
     */
    private function getCurrentQuestion(): Question
    {
        // TODO: What if index out of bounds?
        // TODO: What if question never gets answered?
        // TODO: What if askQuestion called but previous question not answered?
        return $this->quiz->getQuestions()->toArray()[$this->questionIndex];
    }
}
