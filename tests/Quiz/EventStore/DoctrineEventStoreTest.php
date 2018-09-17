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
use VSV\GVQ_API\Quiz\Serializers\AnsweredCorrectDenormalizer;
use VSV\GVQ_API\Quiz\Serializers\AnsweredCorrectNormalizer;
use VSV\GVQ_API\Quiz\Serializers\AnsweredIncorrectDenormalizer;
use VSV\GVQ_API\Quiz\Serializers\AnsweredIncorrectNormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuestionAskedDenormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuestionAskedNormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuizDenormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuizFinishedDenormalizer;
use VSV\GVQ_API\Quiz\Serializers\QuizFinishedNormalizer;
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

        $answerNormalizer = new AnswerNormalizer();
        $questionNormalizer = new QuestionNormalizer(
            new CategoryNormalizer(),
            $answerNormalizer
        );

        $answerDenormalizer = new AnswerDenormalizer();
        $questionDenormalizer = new QuestionDenormalizer(
            new CategoryDenormalizer(),
            $answerDenormalizer
        );

        $normalizers = [
            new QuizStartedNormalizer(
                new QuizNormalizer(
                    new CompanyNormalizer(
                        new TranslatedAliasNormalizer(),
                        new UserNormalizer()
                    ),
                    new PartnerNormalizer(),
                    new TeamNormalizer(),
                    $questionNormalizer
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
                    $questionDenormalizer
                )
            ),
            new QuestionAskedNormalizer($questionNormalizer),
            new QuestionAskedDenormalizer($questionDenormalizer),
            new AnsweredCorrectNormalizer(
                $questionNormalizer,
                $answerNormalizer
            ),
            new AnsweredCorrectDenormalizer(
                $questionDenormalizer,
                $answerDenormalizer
            ),
            new AnsweredIncorrectNormalizer(
                $questionNormalizer,
                $answerNormalizer
            ),
            new AnsweredIncorrectDenormalizer(
                $questionDenormalizer,
                $answerDenormalizer
            ),
            new QuizFinishedNormalizer(),
            new QuizFinishedDenormalizer(),
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
        $domainEvents = $this->createDomainEvents($quiz);
        $domainEventStream = new DomainEventStream($domainEvents);

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
        $domainEvents = $this->createDomainEvents($quiz);

        $this->doctrineEventStore->append(
            $quiz->getId()->toString(),
            new DomainEventStream($domainEvents)
        );

        $actualDomainEventStream = $this->doctrineEventStore->loadFromPlayhead(
            $quiz->getId()->toString(),
            4
        );

        $this->assertEquals(
            new DomainEventStream(
                [
                    $domainEvents[4],
                    $domainEvents[5],
                ]
            ),
            $actualDomainEventStream
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_a_full_domain_event_stream()
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $domainEvents = $this->createDomainEvents($quiz);

        $this->doctrineEventStore->append(
            $quiz->getId()->toString(),
            new DomainEventStream($domainEvents)
        );

        $domainEventStream = $this->doctrineEventStore->getFullDomainEventStream();

        $this->assertEquals(
            new DomainEventStream($domainEvents),
            $domainEventStream
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_does_a_rollback_on_exception_in_append()
    {
        $domainEvent = DomainMessage::recordNow(
            'id',
            0,
            new Metadata(),
            new \StdClass()
        );

        $this->expectException(\Exception::class);

        $this->doctrineEventStore->append(
            'id',
            new DomainEventStream([$domainEvent])
        );
    }

    /**
     * @param Quiz $quiz
     * @return DomainMessage[]
     * @throws \Exception
     */
    private function createDomainEvents(Quiz $quiz): array
    {
        return [
            DomainMessage::recordNow(
                $quiz->getId()->toString(),
                0,
                new Metadata(),
                new QuizStarted($quiz->getId(), $quiz)
            ),
            DomainMessage::recordNow(
                $quiz->getId()->toString(),
                1,
                new Metadata(),
                ModelsFactory::createQuestionAsked()
            ),
            DomainMessage::recordNow(
                $quiz->getId()->toString(),
                2,
                new Metadata(),
                ModelsFactory::createAnsweredCorrect()
            ),
            DomainMessage::recordNow(
                $quiz->getId()->toString(),
                3,
                new Metadata(),
                ModelsFactory::createQuestionAsked()
            ),
            DomainMessage::recordNow(
                $quiz->getId()->toString(),
                4,
                new Metadata(),
                ModelsFactory::createAnsweredIncorrect()
            ),
            DomainMessage::recordNow(
                $quiz->getId()->toString(),
                5,
                new Metadata(),
                ModelsFactory::createQuizFinished()
            )
        ];
    }
}
