<?php

namespace VSV\GVQ_API\Quiz\EventStore;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;

class DoctrineEventStoreTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var DoctrineEventStore
     */
    private $doctrineEventStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctrineEventStore = new DoctrineEventStore(
            $this->entityManager
        );
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return EventEntity::class;
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_append_an_event_stream()
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $this->doctrineEventStore->append(
            $quiz->getId()->toString(),
            $this->createDomainEventStream($quiz)
        );
    }

    /**
     * @param Quiz $quiz
     * @return DomainEventStream
     * @throws \Exception
     */
    private function createDomainEventStream(Quiz $quiz): DomainEventStream
    {
        return new DomainEventStream(
            [
                DomainMessage::recordNow(
                    $quiz->getId()->toString(),
                    0,
                    new Metadata(),
                    new QuizStarted(
                        $quiz->getId(),
                        $quiz
                    )
                ),
                DomainMessage::recordNow(
                    $quiz->getId()->toString(),
                    0,
                    new Metadata(),
                    new QuestionAsked(
                        $quiz->getId(),
                        $quiz->getQuestions()->toArray()[0],
                        new \DateTimeImmutable()
                    )
                ),
            ]
        );
    }
}
