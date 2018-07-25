<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Aggregate;

use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
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
    public function it_can_ask_a_question()
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
                    )
                ]
            );
    }
}
