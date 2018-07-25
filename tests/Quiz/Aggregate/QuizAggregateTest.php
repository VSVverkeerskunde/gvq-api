<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Aggregate;

use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;

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
                    )
                ]
            );
    }
}
