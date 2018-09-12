<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;
use VSV\GVQ_API\Statistics\Serializers\TeamScoreNormalizer;
use VSV\GVQ_API\Statistics\Serializers\TeamScoresNormalizer;
use VSV\GVQ_API\Team\Repositories\TeamRepository;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;

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

    /**
     * @var TeamScoresNormalizer
     */
    private $teamScoresNormalizer;


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

        $this->teamScoresNormalizer = new TeamScoresNormalizer(
            new TeamScoreNormalizer(
                new TeamNormalizer()
            )
        );

        $this->teamService = new TeamService(
            new Year(2018),
            $this->teamRepository,
            $this->teamTotalScoreRepository,
            $this->teamParticipationRepository,
            $this->teamScoresNormalizer
        );
    }

    /**
     * @test
     */
    public function it_can_get_team_ranking_as_json(): void
    {
        $teams = ModelsFactory::createTeams();

        $this->teamRepository->expects($this->once())
            ->method('getAllByYear')
            ->with(new Year(2018))
            ->willReturn($teams);

        $this->teamParticipationRepository->expects($this->exactly($teams->count()))
            ->method('getCountForTeam')
            ->withConsecutive(
                ...$teams->toArray()
            )
            ->willReturnOnConsecutiveCalls(3, 2, 0, 0, 3, 1);
        $this->teamTotalScoreRepository->expects(($this->exactly($teams->count())))
            ->method('getCountForTeam')
            ->withConsecutive(
                ...$teams->toArray()
            )
            ->willReturnOnConsecutiveCalls(10, 16, 0, 0, 10, 3);

        $expectedJson = ModelsFactory::createJson('team_ranking');

        $actualJson = $this->teamService->getTeamRankingAsJson();

        $this->assertEquals(
            $expectedJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_no_teams_found(): void
    {
        $this->teamRepository->expects($this->once())
            ->method('getAllByYear')
            ->with(new Year(2018))
            ->willReturn(null);

        $this->assertEquals(
            '',
            $this->teamService->getTeamRankingAsJson()
        );
    }
}
