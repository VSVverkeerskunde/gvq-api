<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;

class TeamTotalScoreProjectorTest extends QuizFinishedHandlingProjectorTest
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
     * @return Quiz
     * @throws \Exception
     */
    protected function createQuiz(): Quiz
    {
        return ModelsFactory::createCupQuiz();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $domainMessage = $this->createDomainMessage();

        $this->doCommonQuizRepositoryExpect();

        $this->teamTotalScoreRepository->expects($this->once())
            ->method('incrementCountByQuizScore')
            ->with($this->quiz->getTeam(), $this->score);

        $this->teamTotalScoreProjector->handle($domainMessage);
    }
}
