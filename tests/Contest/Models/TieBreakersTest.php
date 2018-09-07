<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class TieBreakersTest extends TestCase
{
    /**
     * @var TieBreaker[]
     */
    private $tieBreakersArray;

    /**
     * @var TieBreakers
     */
    private $tieBreakers;

    protected function setUp(): void
    {
        $this->tieBreakersArray = [
            ModelsFactory::createQuizTieBreaker(),
            ModelsFactory::createCupTieBreaker(),
        ];

        $this->tieBreakers = new TieBreakers(...$this->tieBreakersArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_tie_breakers(): void
    {
        $actualTieBreakers = [];
        foreach ($this->tieBreakers as $tieBreaker) {
            $actualTieBreakers[] = $tieBreaker;
        }

        $this->assertEquals($this->tieBreakersArray, $actualTieBreakers);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(2, count($this->tieBreakers));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->tieBreakersArray,
            $this->tieBreakers->toArray()
        );
    }
}
