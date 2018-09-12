<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class TeamsTest extends TestCase
{
    /**
     * @var Team[]
     */
    private $teamsArray;
    /**
     * @var Teams
     */
    private $teams;

    protected function setUp(): void
    {
        $this->teamsArray = [
            ModelsFactory::createAntwerpTeam(),
            ModelsFactory::createLeuvenTeam(),
            ModelsFactory::createWaaslandTeam(),
            ModelsFactory::createTubizeTeam(),
            ModelsFactory::createLommelTeam(),
            ModelsFactory::createRoeselareTeam()
        ];

        $this->teams = ModelsFactory::createTeams();
    }
    /**
     * @test
     */
    public function it_can_iterate_over_teams(): void
    {
        $actualTeamsArray = [];
        foreach ($this->teams as $team) {
            $actualTeamsArray[] = $team;
        }

        $this->assertEquals($this->teamsArray, $actualTeamsArray);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(6, count($this->teams));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->teamsArray,
            $this->teams->toArray()
        );
    }
}
