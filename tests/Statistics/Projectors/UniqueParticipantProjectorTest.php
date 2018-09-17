<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class UniqueParticipantProjectorTest extends MockedQuizRepositoryTest
{
    /**
     * @var UniqueParticipantRepository|MockObject
     */
    private $uniqueParticipantRepository;

    /**
     * @var QuestionResultRepository|MockObject
     */
    private $questionResultRepository;

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

        /** @var QuestionResultRepository|MockObject $questionResultRepository */
        $questionResultRepository = $this->createMock(QuestionResultRepository::class);
        $this->questionResultRepository = $questionResultRepository;

        $this->uniqueParticipantProjector = new UniqueParticipantProjector(
            $this->uniqueParticipantRepository,
            $this->quizRepository,
            $this->questionResultRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $this->mockQuizRepositoryGetById($quiz);

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('add')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant()
            );

        $this->uniqueParticipantRepository->expects($this->never())
            ->method('addPassed');

        $this->uniqueParticipantProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($quiz)
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished_for_passed_score(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $this->mockQuizRepositoryGetById($quiz);

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('add')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant()
            );

        $this->questionResultRepository->expects($this->once())
            ->method('getById')
            ->with($quiz->getId())
            ->willReturn(ModelsFactory::createQuestionResult());

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('addPassed')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant()
            );

        $this->uniqueParticipantProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($quiz)
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished_for_partner_channel(): void
    {
        $quiz = ModelsFactory::createPartnerQuiz();

        $this->mockQuizRepositoryGetById($quiz);

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

        $this->uniqueParticipantProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($quiz)
        );
    }
}
