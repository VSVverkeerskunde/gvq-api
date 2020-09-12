<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;
use VSV\GVQ_API\Quiz\Events\EmailRegistered;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\AllowedDelay;
use VSV\GVQ_API\User\ValueObjects\Email;

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
     * @var bool
     */
    private $askingQuestion;

    /**
     * @var int
     */
    private $score;

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
        $this->score = 0;
        $this->askingQuestion = false;
    }

    /**
     * @param \DateTimeImmutable $askedOn
     */
    public function askQuestion(\DateTimeImmutable $askedOn): void
    {
        if (!$this->askingQuestion) {
            $this->apply(
                new QuestionAsked(
                    $this->quiz->getId(),
                    $this->getCurrentQuestion(),
                    $askedOn
                )
            );
        }
    }

    /**
     * @param QuestionAsked $questionAsked
     */
    protected function applyQuestionAsked(QuestionAsked $questionAsked): void
    {
        $this->questionAskedOn = $questionAsked->getAskedOn();
        $this->askingQuestion = true;
    }

    /**
     * @param \DateTimeImmutable $answeredOn
     * @param Answer|null $answer
     */
    public function answerQuestion(
        \DateTimeImmutable $answeredOn,
        ?Answer $answer
    ): void {
        if ($this->askingQuestion) {
            $currentQuestion = $this->getCurrentQuestion();
            $answeredTooLate = $this->answeredTooLate(
                $this->questionAskedOn,
                $answeredOn,
                $this->quiz->getAllowedDelay()
            );

            // When the timer finishes in the quiz frontend an empty answered is send.
            // But to make sure that no question is answered too late, always check the elapsed time.
            if ($answeredTooLate || $answer === null) {
                $this->apply(
                    new AnsweredTooLate(
                        $this->quiz->getId(),
                        $currentQuestion,
                        $answeredOn
                    )
                );
            } elseif (!$this->answeredCorrect($currentQuestion->getAnswers(), $answer)) {
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

            if (count($this->quiz->getQuestions()) === ($this->questionIndex)) {
                $this->apply(
                    new QuizFinished(
                        $this->quiz->getId(),
                        $this->score
                    )
                );
            }
        }
    }

    protected function applyAnsweredIncorrect(): void
    {
        $this->questionIndex++;
        $this->askingQuestion = false;
    }

    protected function applyAnsweredTooLate(): void
    {
        $this->questionIndex++;
        $this->askingQuestion = false;
    }

    protected function applyAnsweredCorrect(): void
    {
        $this->score += 1;
        $this->questionIndex++;
        $this->askingQuestion = false;
    }

    protected function applyQuizFinished(): void
    {
        $this->questionIndex = -1;
        $this->askingQuestion = false;
    }

    /**
     * @param \DateTimeImmutable $askedOn
     * @param \DateTimeImmutable $answeredOn
     * @param AllowedDelay $allowedDelay
     * @return bool
     */
    private function answeredTooLate(
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
        return $this->quiz->getQuestions()->toArray()[$this->questionIndex];
    }

    public function registerEmail(Email $email) {
        $this->apply(
            new EmailRegistered(
                $this->quiz->getId(),
                $email
            )
        );
    }
}
