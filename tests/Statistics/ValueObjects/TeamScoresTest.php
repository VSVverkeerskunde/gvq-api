<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class TeamScoresTest extends TestCase
{
    /**
     * @var TeamScore[]
     */
    private $teamScoresArray;

    /**
     * @var TeamScore[]
     */
    private $sortedTeamScoresArray;

    /**
     * @var TeamScores
     */
    private $teamScores;

    protected function setUp(): void
    {
        $this->teamScoresArray = [
            ModelsFactory::createTubizeTeamScore(),
            ModelsFactory::createLommelTeamScore(),
            ModelsFactory::createWaaslandTeamScore(),
            ModelsFactory::createLeuvenTeamScore(),
            ModelsFactory::createBruggeTeamScore(),
            ModelsFactory::createAntwerpTeamScore(),
            ModelsFactory::createRoeselareTeamScore(),
        ];

        $this->sortedTeamScoresArray = [
            ModelsFactory::createLeuvenTeamScore(),
            ModelsFactory::createLommelTeamScore(),
            ModelsFactory::createAntwerpTeamScore(),
            ModelsFactory::createBruggeTeamScore(),
            ModelsFactory::createRoeselareTeamScore(),
            ModelsFactory::createTubizeTeamScore(),
            ModelsFactory::createWaaslandTeamScore(),
        ];

        $this->teamScores = new TeamScores(...$this->teamScoresArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_teamScores(): void
    {
        $actualTeamScoresArray = [];
        foreach ($this->teamScores as $teamScore) {
            $actualTeamScoresArray[] = $teamScore;
        }

        $this->assertEquals($this->sortedTeamScoresArray, $actualTeamScoresArray);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(7, count($this->teamScores));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->sortedTeamScoresArray,
            $this->teamScores->toArray()
        );
    }
}
