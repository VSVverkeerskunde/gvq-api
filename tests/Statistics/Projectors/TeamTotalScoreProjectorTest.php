<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;

class TeamTotalScoreProjectorTest extends MockedQuizRepositoryTest
{
    /**
     * @var TeamTotalScoreRepository|MockObject
     */
    private $teamTotalScoreRepository;

    /**
     * @var TeamTotalScoreProjector
     */
    private $teamTotalScoreProjector;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var TeamTotalScoreRepository|MockObject $teamTotalScoreRepository */
        $teamTotalScoreRepository = $this->createMock(TeamTotalScoreRepository::class);
        $this->teamTotalScoreRepository = $teamTotalScoreRepository;

        $this->teamTotalScoreProjector = new TeamTotalScoreProjector(
            $this->teamTotalScoreRepository,
            $this->quizRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $quiz = ModelsFactory::createCupQuiz();

        $this->mockQuizRepositoryGetById($quiz);

        $this->teamTotalScoreRepository->expects($this->once())
            ->method('incrementTotalScoreByQuizScore')
            ->with($quiz->getTeam(), 10);

        $this->teamTotalScoreProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($quiz)
        );
    }
}
