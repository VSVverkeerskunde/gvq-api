<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Contest\Repositories\ContestParticipationRepository;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;

class ContestServiceTest extends TestCase
{
    /**
     * @var QuestionResultRepository|MockObject
     */
    private $questionResultRepository;

    /**
     * @var QuizRepository|MockObject
     */
    private $quizRepository;

    /**
     * @var ContestParticipationRepository|MockObject
     */
    private $contestParticipationRepository;

    /**
     * @var ContestService
     */
    private $contestService;

    protected function setUp(): void
    {
        /** @var QuestionResultRepository|MockObject $questionResultRepository */
        $questionResultRepository = $this->createMock(QuestionResultRepository::class);
        $this->questionResultRepository = $questionResultRepository;

        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        /** @var ContestParticipationRepository|MockObject $contestParticipationRepository */
        $contestParticipationRepository = $this->createMock(ContestParticipationRepository::class);
        $this->contestParticipationRepository = $contestParticipationRepository;

        $this->contestService = new ContestService(
            $this->questionResultRepository,
            $this->quizRepository,
            $this->contestParticipationRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_is_possible_to_participate_when_no_previous_participation_present(): void
    {
        $passedQuestionResult = ModelsFactory::createCustomQuestionResult(
            ModelsFactory::createGeneralQuestion(),
            false,
            11
        );
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');

        $this->questionResultRepository->expects($this->once())
            ->method('getById')
            ->with($quizId)
            ->willReturn($passedQuestionResult);

        $quiz = ModelsFactory::createIndividualQuiz();

        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($quizId)
            ->willReturn($quiz);

        $this->contestParticipationRepository->expects($this->once())
            ->method('getByYearAndEmailAndChannel')
            ->with(
                new Year(2018),
                $quiz->getParticipant()->getEmail(),
                $quiz->getChannel()
            )
            ->willReturn(
                null
            );

        $this->assertTrue(
            $this->contestService->canParticipate(
                new Year(2018),
                $quizId
            )
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_is_not_possible_to_participate_when_score_lower_then_11(): void
    {
        $failedQuestionResult = ModelsFactory::createCustomQuestionResult(
            ModelsFactory::createGeneralQuestion(),
            false,
            10
        );
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');

        $this->questionResultRepository->expects($this->once())
            ->method('getById')
            ->with($quizId)
            ->willReturn($failedQuestionResult);

        $this->assertFalse(
            $this->contestService->canParticipate(
                new Year(2018),
                $quizId
            )
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_is_not_possible_to_participate_when_already_participated_for_same_channel(): void
    {
        $passedQuestionResult = ModelsFactory::createCustomQuestionResult(
            ModelsFactory::createGeneralQuestion(),
            false,
            11
        );
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');

        $this->questionResultRepository->expects($this->once())
            ->method('getById')
            ->with($quizId)
            ->willReturn($passedQuestionResult);

        $quiz = ModelsFactory::createIndividualQuiz();

        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($quizId)
            ->willReturn($quiz);

        $this->contestParticipationRepository->expects($this->once())
            ->method('getByYearAndEmailAndChannel')
            ->with(
                new Year(2018),
                $quiz->getParticipant()->getEmail(),
                $quiz->getChannel()
            )
            ->willReturn(
                ModelsFactory::createCupContestParticipation()
            );

        $this->assertFalse(
            $this->contestService->canParticipate(
                new Year(2018),
                $quizId
            )
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_save_a_contest_participation(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $this->contestParticipationRepository->expects($this->once())
            ->method('getByYearAndEmailAndChannel')
            ->with(
                new Year(2018),
                $quiz->getParticipant()->getEmail(),
                $quiz->getChannel()
            )
            ->willReturn(
                null
            );

        $contestParticipation = ModelsFactory::createQuizContestParticipation();
        $this->contestParticipationRepository->expects($this->once())
            ->method('save')
            ->with($contestParticipation);

        $this->contestService->save($contestParticipation);
    }
}
