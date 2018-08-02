<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventStore;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Company\Serializers\CompanyDenormalizer;
use VSV\GVQ_API\Company\Serializers\CompanyNormalizer;
use VSV\GVQ_API\Company\Serializers\TranslatedAliasDenormalizer;
use VSV\GVQ_API\Company\Serializers\TranslatedAliasNormalizer;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Serializers\PartnerDenormalizer;
use VSV\GVQ_API\Partner\Serializers\PartnerNormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryDenormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\Serializers\QuizDenormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuizNormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuizStartedDenormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuizStartedNormalizer;
use VSV\GVQ_API\Team\Serializers\TeamDenormalizer;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class DoctrineEventStoreTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var DoctrineEventStore
     */
    private $doctrineEventStore;

    protected function setUp(): void
    {
        parent::setUp();

        $normalizers = [
            new QuizStartedNormalizer(
                new QuizNormalizer(
                    new CompanyNormalizer(
                        new TranslatedAliasNormalizer(),
                        new UserNormalizer()
                    ),
                    new PartnerNormalizer(),
                    new TeamNormalizer(),
                    new QuestionNormalizer(
                        new CategoryNormalizer(),
                        new AnswerNormalizer()
                    )
                )
            ),
            new QuizStartedDenormalizer(
                new QuizDenormalizer(
                    new CompanyDenormalizer(
                        new TranslatedAliasDenormalizer(),
                        new UserDenormalizer()
                    ),
                    new PartnerDenormalizer(),
                    new TeamDenormalizer(),
                    new QuestionDenormalizer(
                        new CategoryDenormalizer(),
                        new AnswerDenormalizer()
                    )
                )
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        $this->doctrineEventStore = new DoctrineEventStore(
            $this->entityManager,
            $serializer
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
    public function it_can_append_and_load_an_event_stream()
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $domainEventStream = $this->createDomainEventStream($quiz);

        $this->doctrineEventStore->append(
            $quiz->getId()->toString(),
            $domainEventStream
        );

        $actualDomainEventStream = $this->doctrineEventStore->load(
            $quiz->getId()->toString()
        );

        $this->assertEquals(
            $domainEventStream,
            $actualDomainEventStream
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_load_an_event_stream_from_given_play_head()
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $domainEventStream = $this->createDomainEventStream($quiz);

        $this->doctrineEventStore->append(
            $quiz->getId()->toString(),
            $domainEventStream
        );

        // TODO: load from other playhead.
        $actualDomainEventStream = $this->doctrineEventStore->loadFromPlayhead(
            $quiz->getId()->toString(),
            1
        );

        $this->assertEquals(
            new DomainEventStream([]),
            $actualDomainEventStream
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
                // TODO: Add normalizer and denormalizer for QuestionAsked and also test.
            ]
        );
    }
}
