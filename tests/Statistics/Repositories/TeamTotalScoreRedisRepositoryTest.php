<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Team\Models\Team;

class TeamTotalScoreRedisRepositoryTest extends TestCase
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
     * @var TeamTotalScoreRepository
     */
    private $teamTotalScoreRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        $this->teamTotalScoreRepository = new TeamTotalScoreRedisRepository(
            $this->redis
        );

        $this->team = ModelsFactory::createTeam();
    }

    /**
     * @test
     */
    public function it_can_increment_count_by_quiz_score(): void
    {
        $score = 11;

        $this->redis->expects($this->once())
            ->method('incrBy')
            ->with('team_total_score_'.$this->team->getId()->toString(), $score);

        $this->teamTotalScoreRepository->incrementCountByQuizScore($this->team, $score);
    }

    /**
     * @test
     */
    public function it_can_get_count_of_team_total_score(): void
    {
        $this->redis->expects($this->once())
            ->method('get')
            ->with('team_total_score_'.$this->team->getId()->toString());

        $this->teamTotalScoreRepository->getCountForTeam($this->team);
    }
}
