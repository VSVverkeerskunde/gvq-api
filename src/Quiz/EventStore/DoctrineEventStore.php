<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventStore;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventStore\EventStore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;

// Simplified from https://github.com/broadway/event-store-dbal
class DoctrineEventStore extends AbstractDoctrineRepository implements EventStore
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($entityManager);

        $this->serializer = $serializer;
    }

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
        return $this->getDomainEventStream($id, 0);
    }

    /**
     * @inheritdoc
     */
    public function loadFromPlayhead($id, int $playhead): DomainEventStream
    {
        return $this->getDomainEventStream($id, $playhead);
    }

    /**
     * @return \Traversable
     */
    public function getTraversableDomainMessages(): \Traversable
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $query = $queryBuilder->select('e')
            ->from('VSV\GVQ_API\Quiz\EventStore\EventEntity', 'e')
            ->orderBy('e.id', 'ASC')
            ->getQuery();

        foreach ($query->iterate() as $eventEntities) {
            yield $this->createDomainMessage($eventEntities[0]);
        }
    }

    /**
     * @param string $id
     * @param int $playhead
     * @return DomainEventStream
     */
    private function getDomainEventStream(string $id, int $playhead): DomainEventStream
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e')
            ->from('VSV\GVQ_API\Quiz\EventStore\EventEntity', 'e')
            ->where('e.uuid = :uuid')
            ->andWhere('e.playhead >= :playhead')
            ->orderBy('e.playhead', 'ASC')
            ->setParameter('uuid', $id)
            ->setParameter('playhead', $playhead);

        $eventEntities = $queryBuilder->getQuery()->getResult();

        return $this->createDomainEventStream($eventEntities);
    }

    /**
     * @inheritdoc
     * @throws \Exception
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
            throw $exception;
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
            $this->serializer->serialize(
                $domainMessage->getPayload(),
                'json'
            ),
            '',
            $domainMessage->getRecordedOn()->toString(),
            $domainMessage->getType()
        );
    }

    /**
     * @param EventEntity[] $eventEntities
     * @return DomainEventStream
     */
    private function createDomainEventStream(array $eventEntities): DomainEventStream
    {
        $domainMessages = [];
        foreach ($eventEntities as $eventEntity) {
            $domainMessages[] = $this->createDomainMessage($eventEntity);
        }

        return new DomainEventStream($domainMessages);
    }

    /**
     * @param EventEntity $eventEntity
     * @return DomainMessage
     */
    private function createDomainMessage(EventEntity $eventEntity): DomainMessage
    {
        return new DomainMessage(
            $eventEntity->getUuid(),
            $eventEntity->getPlayhead(),
            new Metadata(),
            $this->serializer->deserialize(
                $eventEntity->getPayload(),
                $eventEntity->getType(),
                'json'
            ),
            DateTime::fromString($eventEntity->getRecordedOn())
        );
    }
}
