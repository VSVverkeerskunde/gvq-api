<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class UniqueParticipantProjectorTest extends QuizFinishedHandlingProjectorTest
{
    /**
     * @var UniqueParticipantRepository|MockObject
     */
    private $uniqueParticipantRepository;

    /**
     * @var UniqueParticipantProjector
     */
    private $uniqueParticipantProjector;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UniqueParticipantRepository|MockObject $uniqueParticipantRepository */
        $uniqueParticipantRepository = $this->createMock(UniqueParticipantRepository::class);
        $this->uniqueParticipantRepository = $uniqueParticipantRepository;

        $this->uniqueParticipantProjector = new UniqueParticipantProjector(
            $this->uniqueParticipantRepository,
            $this->quizRepository
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function createQuiz(): Quiz
    {
        return ModelsFactory::createIndividualQuiz();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $domainMessage = $this->createDomainMessage();

        $this->doCommonQuizRepositoryExpect();

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('add')
            ->with(
                StatisticsKey::createFromQuiz($this->quiz),
                $this->quiz->getParticipant()
            );

        $this->uniqueParticipantProjector->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished_for_partner_channel(): void
    {
        $quiz = ModelsFactory::createPartnerQuiz();

        $domainMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished(
                $quiz->getId(),
                11
            )
        );

        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($quiz->getId())
            ->willReturn($quiz);

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('add')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant()
            );

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('addForPartner')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant(),
                $quiz->getPartner()
            );

        $this->uniqueParticipantProjector->handle($domainMessage);
    }
}
