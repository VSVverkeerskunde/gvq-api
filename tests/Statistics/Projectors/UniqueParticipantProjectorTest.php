<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class UniqueParticipantProjectorTest extends MockedQuizRepositoryTest
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
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $this->mockQuizRepositoryGetById($quiz);

        $this->uniqueParticipantRepository->expects($this->exactly(6))
            ->method('add')
            ->withConsecutive(
                [
                    StatisticsKey::createFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createChannelTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createQuizTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::QUIZ_TOT),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createOverallTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::OVERALL_TOT),
                    $quiz->getParticipant(),
                ]
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
    public function it_handles_fr_quiz_finished(): void
    {
        $quiz = ModelsFactory::createIndividualFrQuiz();

        $this->mockQuizRepositoryGetById($quiz);

        $this->uniqueParticipantRepository->expects($this->exactly(6))
            ->method('add')
            ->withConsecutive(
                [
                    StatisticsKey::createFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createChannelTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createQuizTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::QUIZ_TOT),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createOverallTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::OVERALL_TOT),
                    $quiz->getParticipant(),
                ]
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

        $this->uniqueParticipantRepository->expects($this->exactly(6))
            ->method('add')
            ->withConsecutive(
                [
                    StatisticsKey::createFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createChannelTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createQuizTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::QUIZ_TOT),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createOverallTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::OVERALL_TOT),
                    $quiz->getParticipant(),
                ]
            );

        $this->uniqueParticipantRepository->expects($this->exactly(6))
            ->method('addPassed')
            ->withConsecutive(
                [
                    StatisticsKey::createFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createChannelTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createQuizTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::QUIZ_TOT),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createOverallTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::OVERALL_TOT),
                    $quiz->getParticipant(),
                ]
            );

        $this->uniqueParticipantProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($quiz, 12)
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

        $this->uniqueParticipantRepository->expects($this->exactly(6))
            ->method('add')
            ->withConsecutive(
                [
                    StatisticsKey::createFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createChannelTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createQuizTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::QUIZ_TOT),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createOverallTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::OVERALL_TOT),
                    $quiz->getParticipant(),
                ]
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

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished_for_cup_channel(): void
    {
        $quiz = ModelsFactory::createCupQuiz();

        $this->mockQuizRepositoryGetById($quiz);

        $this->uniqueParticipantRepository->expects($this->exactly(4))
            ->method('add')
            ->withConsecutive(
                [
                    StatisticsKey::createFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createChannelTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    StatisticsKey::createOverallTotalFromQuiz($quiz),
                    $quiz->getParticipant(),
                ],
                [
                    new StatisticsKey(StatisticsKey::OVERALL_TOT),
                    $quiz->getParticipant(),
                ]
            );

        $this->uniqueParticipantRepository->expects($this->never())
            ->method('addForPartner');

        $this->uniqueParticipantProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($quiz)
        );
    }
}
