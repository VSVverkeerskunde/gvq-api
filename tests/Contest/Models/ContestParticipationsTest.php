<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class ContestParticipationsTest extends TestCase
{
    /**
     * @var ContestParticipation[]
     */
    private $contestParticipationsArray;

    /**
     * @var ContestParticipations
     */
    private $contestParticipations;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->contestParticipationsArray = [
            ModelsFactory::createQuizContestParticipation(),
            ModelsFactory::createCupContestParticipation()
        ];

        $this->contestParticipations = new ContestParticipations(
            ...$this->contestParticipationsArray
        );
    }

    /**
     * @test
     */
    public function it_can_iterate_over_contest_participations(): void
    {
        $actualContestParticipations = [];
        foreach ($this->contestParticipations as $contestParticipation) {
            $actualContestParticipations[] = $contestParticipation;
        }

        $this->assertEquals(
            $this->contestParticipationsArray,
            $actualContestParticipations
        );
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(
            2,
            count($this->contestParticipations)
        );
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->contestParticipationsArray,
            $this->contestParticipations->toArray()
        );
    }
}
