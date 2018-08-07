<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use VSV\GVQ_API\Quiz\Aggregate\QuizAggregate;

class QuizAggregateRepository extends EventSourcingRepository
{
    /**
     * @param EventStore $eventStore
     * @param EventBus $eventBus
     * @param array $eventStreamDecorators
     */
    public function __construct(
        EventStore $eventStore,
        EventBus $eventBus,
        $eventStreamDecorators = []
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            QuizAggregate::class,
            new PublicConstructorAggregateFactory(),
            $eventStreamDecorators
        );
    }
}
