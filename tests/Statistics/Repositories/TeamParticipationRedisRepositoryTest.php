<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Team\Models\Team;

class TeamParticipationRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var Team
     */
    private $team;

    /**
     * @var TeamParticipationRepository
     */
    private $teamParticipationRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        $this->teamParticipationRepository = new TeamParticipationRedisRepository(
            $this->redis
        );

        $this->team = ModelsFactory::createAntwerpTeam();
    }

    /**
     * @test
     */
    public function it_can_increment_count_of_team_participations(): void
    {
        $this->redis->expects($this->once())
            ->method('incr')
            ->with('team_participations_'.$this->team->getId()->toString());

        $this->teamParticipationRepository->incrementCountForTeam($this->team);
    }

    /**
     * @test
     */
    public function it_can_get_total_count_of_team_participations(): void
    {
        $this->redis->expects($this->once())
            ->method('get')
            ->with('team_participations_'.$this->team->getId()->toString());

        $this->teamParticipationRepository->getCountForTeam($this->team);
    }
}
