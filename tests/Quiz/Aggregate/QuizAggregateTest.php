<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Aggregate;

use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;

/**
 * @group time-sensitive
 */
class QuizAggregateTest extends AggregateRootScenarioTestCase
{
    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->quiz = ModelsFactory::createQuiz();
    }

    /**
     * @inheritdoc
     */
    protected function getAggregateRootClass(): string
    {
        return QuizAggregate::class;
    }

    /**
     * @test
     */
    public function it_can_start_a_quiz()
    {
        $this->scenario
            ->when(function () {
                return QuizAggregate::start($this->quiz);
            })
            ->then(
                [
                    new QuizStarted(
                        $this->quiz->getId(),
                        $this->quiz
                    ),
                ]
            );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_triggers_quiz_started_event()
    {
        $askedOn = new \DateTimeImmutable();

        $this->scenario
            ->withAggregateId($this->quiz->getId()->toString())
            ->given(
                [
                    new QuizStarted(
                        $this->quiz->getId(),
                        $this->quiz
                    ),
                ]
            )
            ->when(function (QuizAggregate $quizAggregate) use ($askedOn) {
                $quizAggregate->askQuestion($askedOn);
            })
            ->then(
                [
                    new QuestionAsked(
                        $this->quiz->getId(),
                        $this->quiz->getQuestions()->toArray()[0],
                        $askedOn
                    ),
                ]
            );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_triggers_answered_incorrect_event_when_answered_too_late()
    {
        $askedOn = new \DateTimeImmutable();
        $question = $this->quiz->getQuestions()->toArray()[0];

        $answeredOn = $askedOn->add(new \DateInterval('PT50S'));
        $correctAnswer = $question->getAnswers()->toArray()[2];

        $this->scenario
            ->withAggregateId($this->quiz->getId()->toString())
            ->given(
                [
                    new QuizStarted(
                        $this->quiz->getId(),
                        $this->quiz
                    ),
                    new QuestionAsked(
                        $this->quiz->getId(),
                        $question,
                        $askedOn
                    ),
                ]
            )
            ->when(function (QuizAggregate $quizAggregate) use ($answeredOn, $correctAnswer) {
                $quizAggregate->answerQuestion(
                    $answeredOn,
                    $correctAnswer
                );
            })
            ->then(
                [
                    new AnsweredIncorrect(
                        $this->quiz->getId(),
                        $question,
                        $correctAnswer,
                        $answeredOn
                    ),
                ]
            );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_triggers_answered_incorrect_event_when_answered_wrong()
    {
        $askedOn = new \DateTimeImmutable();
        $question = $this->quiz->getQuestions()->toArray()[0];

        $answeredOn = $askedOn->add(new \DateInterval('PT30S'));
        $inCorrectAnswer = $question->getAnswers()->toArray()[1];

        $this->scenario
            ->withAggregateId($this->quiz->getId()->toString())
            ->given(
                [
                    new QuizStarted(
                        $this->quiz->getId(),
                        $this->quiz
                    ),
                    new QuestionAsked(
                        $this->quiz->getId(),
                        $question,
                        $askedOn
                    ),
                ]
            )
            ->when(function (QuizAggregate $quizAggregate) use ($answeredOn, $inCorrectAnswer) {
                $quizAggregate->answerQuestion(
                    $answeredOn,
                    $inCorrectAnswer
                );
            })
            ->then(
                [
                    new AnsweredIncorrect(
                        $this->quiz->getId(),
                        $question,
                        $inCorrectAnswer,
                        $answeredOn
                    ),
                ]
            );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_triggers_answered_correct_event_when_answered_correct()
    {
        $askedOn = new \DateTimeImmutable();
        $question = $this->quiz->getQuestions()->toArray()[0];

        $answeredOn = $askedOn->add(new \DateInterval('PT30S'));
        $correctAnswer = $question->getAnswers()->toArray()[2];

        $this->scenario
            ->withAggregateId($this->quiz->getId()->toString())
            ->given(
                [
                    new QuizStarted(
                        $this->quiz->getId(),
                        $this->quiz
                    ),
                    new QuestionAsked(
                        $this->quiz->getId(),
                        $question,
                        $askedOn
                    ),
                ]
            )
            ->when(function (QuizAggregate $quizAggregate) use ($answeredOn, $correctAnswer) {
                $quizAggregate->answerQuestion(
                    $answeredOn,
                    $correctAnswer
                );
            })
            ->then(
                [
                    new AnsweredCorrect(
                        $this->quiz->getId(),
                        $question,
                        $correctAnswer,
                        $answeredOn
                    ),
                ]
            );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_multiple_questions()
    {
        $question1AskedOn = new \DateTimeImmutable();
        $question1 = $this->quiz->getQuestions()->toArray()[0];

        $question1AnsweredOn = $question1AskedOn->add(new \DateInterval('PT30S'));
        $question1Answer = $question1->getAnswers()->toArray()[2];

        $question2AskedOn = $question1AnsweredOn->add(new \DateInterval('PT2S'));
        $question2 = $this->quiz->getQuestions()->toArray()[1];

        $question2AnsweredOn = $question2AskedOn->add(new \DateInterval('PT30S'));
        $question2Answer = $question2->getAnswers()->toArray()[2];

        $this->scenario
            ->withAggregateId($this->quiz->getId()->toString())
            ->given(
                [
                    new QuizStarted(
                        $this->quiz->getId(),
                        $this->quiz
                    ),
                ]
            )
            ->when(function (QuizAggregate $quizAggregate) use (
                $question1AskedOn,
                $question1AnsweredOn,
                $question1Answer,
                $question2AskedOn,
                $question2AnsweredOn,
                $question2Answer
            ) {
                $quizAggregate->askQuestion($question1AskedOn);
                $quizAggregate->answerQuestion(
                    $question1AnsweredOn,
                    $question1Answer
                );

                $quizAggregate->askQuestion($question2AskedOn);
                $quizAggregate->answerQuestion(
                    $question2AnsweredOn,
                    $question2Answer
                );
            })
            ->then(
                [
                    new QuestionAsked(
                        $this->quiz->getId(),
                        $question1,
                        $question1AskedOn
                    ),
                    new AnsweredCorrect(
                        $this->quiz->getId(),
                        $question1,
                        $question1Answer,
                        $question1AnsweredOn
                    ),
                    new QuestionAsked(
                        $this->quiz->getId(),
                        $question2,
                        $question2AskedOn
                    ),
                    new AnsweredCorrect(
                        $this->quiz->getId(),
                        $question2,
                        $question2Answer,
                        $question2AnsweredOn
                    ),
                    new QuizFinished(
                        $this->quiz->getId()
                    ),
                ]
            );
    }
}
