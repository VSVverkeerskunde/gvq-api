<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Broadway\EventHandling\SimpleEventBus;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\InMemoryEventStore;
use PHPUnit\Framework\TestCase;

class QuizAggregateRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $quizAggregateRepository = new QuizAggregateRepository(
            new InMemoryEventStore(),
            new SimpleEventBus()
        );

        $this->assertTrue(
            $quizAggregateRepository instanceof EventSourcingRepository
        );
    }
}
