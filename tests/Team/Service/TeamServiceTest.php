<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScore;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScores;
use VSV\GVQ_API\Team\Repositories\TeamRepository;

class TeamServiceTest extends TestCase
{
    /**
     * @var TeamService
     */
    private $teamService;

    /**
     * @var TeamRepository|MockObject
     */
    private $teamRepository;

    /**
     * @var TeamTotalScoreRepository|MockObject
     */
    private $teamTotalScoreRepository;

    /**
     * @var TeamParticipationRepository|MockObject
     */
    private $teamParticipationRepository;

    public function setUp(): void
    {
        /** @var TeamRepository|MockObject $teamRepository */
        $teamRepository = $this->createMock(TeamRepository::class);
        $this->teamRepository = $teamRepository;

        /** @var TeamTotalScoreRepository|MockObject $teamTotalScoreRepository */
        $teamTotalScoreRepository = $this->createMock(TeamTotalScoreRepository::class);
        $this->teamTotalScoreRepository = $teamTotalScoreRepository;

        /** @var TeamParticipationRepository|MockObject $teamParticipationRepository */
        $teamParticipationRepository = $this->createMock(TeamParticipationRepository::class);
        $this->teamParticipationRepository = $teamParticipationRepository;

        $this->teamService = new TeamService(
            new Year(2018),
            $this->teamRepository,
            $this->teamTotalScoreRepository,
            $this->teamParticipationRepository
        );
    }

    /**
     * @test
     */
    public function it_can_get_ranked_team_scores(): void
    {
        $teams = ModelsFactory::createTeams();

        $this->teamRepository->expects($this->once())
            ->method('getAllByYear')
            ->with(new Year(2018))
            ->willReturn($teams);

        $this->teamParticipationRepository->expects($this->exactly($teams->count()))
            ->method('getForTeam')
            ->withConsecutive(
                ...$teams->toArray()
            )
            ->willReturnOnConsecutiveCalls(3, 2, 0, 0, 3, 1);
        $this->teamTotalScoreRepository->expects(($this->exactly($teams->count())))
            ->method('getForTeam')
            ->withConsecutive(
                ...$teams->toArray()
            )
            ->willReturnOnConsecutiveCalls(10, 16, 0, 0, 10, 3);

        $expectedTeamScores = ModelsFactory::createRankedTeamScores();

        /** @var TeamScores $actualTeamScores */
        $actualTeamScores = $this->teamService->getRankedTeamScores();

        $iterator = 0;
        foreach ($expectedTeamScores as $expectedTeamScore) {
            /** @var TeamScore $expectedTeamScore */
            $this->assertEquals(
                $expectedTeamScore->getTeam(),
                $actualTeamScores->toArray()[$iterator]->getTeam()
            );

            $iterator++;
        }
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_teams_found(): void
    {
        $this->teamRepository->expects($this->once())
            ->method('getAllByYear')
            ->with(new Year(2018))
            ->willReturn(null);

        $this->assertNull(
            $this->teamService->getRankedTeamScores()
        );
    }
}
