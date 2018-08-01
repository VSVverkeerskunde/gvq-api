<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventStore;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\EventStore\EventStore;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;

// Simplified from https://github.com/broadway/event-store-dbal
class DoctrineEventStore extends AbstractDoctrineRepository implements EventStore
{
    /**
     * @return string
     */
    protected function getRepositoryName(): string
    {
        return EventEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function load($id): DomainEventStream
    {
        // TODO: Implement load() method.
    }

    /**
     * @inheritdoc
     */
    public function loadFromPlayhead($id, int $playhead): DomainEventStream
    {
        // TODO: Implement loadFromPlayhead() method.
    }

    /**
     * @inheritdoc
     */
    public function append($id, DomainEventStream $eventStream)
    {
        $this->entityManager->beginTransaction();

        try {
            /** @var DomainMessage $domainMessage */
            foreach ($eventStream as $domainMessage) {
                $this->entityManager->persist(
                    $this->createEventEntity($domainMessage)
                );
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
        }
    }

    /**
     * @param DomainMessage $domainMessage
     * @return EventEntity
     */
    private function createEventEntity(DomainMessage $domainMessage): EventEntity
    {
        return new EventEntity(
            $domainMessage->getId(),
            $domainMessage->getPlayhead(),
            $domainMessage->getPayload(),
            json_encode($domainMessage->getMetadata()->serialize()),
            $domainMessage->getRecordedOn()->toString(),
            $domainMessage->getType()
        );
    }
}
