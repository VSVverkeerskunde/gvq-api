<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRepository;

class TeamParticipationsProjectorTest extends QuizFinishedHandlingProjectorTest
{
    /**
     * @var TeamParticipationRepository|MockObject
     */
    private $teamParticipationRepository;

    /**
     * @var TeamParticipationsProjector
     */
    private $teamParticipationsProjector;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var TeamParticipationRepository|MockObject $teamParticipationRepository */
        $teamParticipationRepository = $this->createMock(TeamParticipationRepository::class);
        $this->teamParticipationRepository = $teamParticipationRepository;

        $this->teamParticipationsProjector = new TeamParticipationsProjector(
            $this->teamParticipationRepository,
            $this->quizRepository
        );
    }

    /**
     * @inheritdoc
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
        $this->mockQuizRepositoryGetById();

        $this->teamParticipationRepository->expects($this->once())
            ->method('incrementCountForTeam')
            ->with($this->quiz->getTeam());

        $this->teamParticipationsProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($this->quiz)
        );
    }
}
