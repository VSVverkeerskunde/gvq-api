<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Repositories;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Team\Models\Team;

class TeamYamlRepositoryTest extends TestCase
{
    /**
     * @var TeamYamlRepository
     */
    private $teamYamlRepository;

    /**
     * @var Team
     */
    private $team;

    protected function setUp(): void
    {
        $this->teamYamlRepository = new TeamYamlRepository(
            __DIR__.'/../../Factory/Samples/teams.yaml'
        );

        $this->team = ModelsFactory::createTeam();
    }

    /**
     * @test
     */
    public function it_can_get_a_team_by_year_and_id(): void
    {
        $foundTeam = $this->teamYamlRepository->getByYearAndId(
            new Year(2018),
            Uuid::fromString('5c128cad-8727-4e3e-bfba-c51929ae14c4')
        );

        $this->assertEquals(
            $this->team,
            $foundTeam
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_id_does_not_exist(): void
    {
        $foundTeam = $this->teamYamlRepository->getByYearAndId(
            new Year(2018),
            Uuid::fromString('1c987d3c-665e-4ad5-96be-390d4a588515')
        );

        $this->assertNull($foundTeam);
    }

    /**
     * @test
     */
    public function it_returns_null_when_year_is_not_present(): void
    {
        $foundTeam = $this->teamYamlRepository->getByYearAndId(
            new Year(2019),
            Uuid::fromString('5c128cad-8727-4e3e-bfba-c51929ae14c4')
        );

        $this->assertNull($foundTeam);
    }
}
