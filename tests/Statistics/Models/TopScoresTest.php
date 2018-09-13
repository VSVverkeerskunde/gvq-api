<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScoresTest extends TestCase
{
    /**
     * @var TopScore[]
     */
    private $topScoresArray;

    /**
     * @var TopScore[]
     */
    private $sortedTopScoresArray;

    /**
     * @var TopScores
     */
    private $topScores;

    protected function setUp(): void
    {
        $this->topScoresArray = [
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('john@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(12)
            ),
            new TopScore(
                new Email('john@vsv.be'),
                new NaturalNumber(12)
            ),
            new TopScore(
                new Email('ell@vsv.be'),
                new NaturalNumber(10)
            ),
            new TopScore(
                new Email('denver@vsv.be'),
                new NaturalNumber(11)
            ),
        ];

        $this->sortedTopScoresArray = [
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(12)
            ),
            new TopScore(
                new Email('john@vsv.be'),
                new NaturalNumber(12)
            ),
            new TopScore(
                new Email('denver@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('john@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('ell@vsv.be'),
                new NaturalNumber(10)
            ),
        ];

        $this->topScores = new TopScores(...$this->topScoresArray);
    }

    /**
     * @test
     */
    public function it_can_be_iterated(): void
    {
        $iteratedTopScores = [];

        foreach ($this->topScores as $topScore) {
            $iteratedTopScores[] = $topScore;
        }

        $this->assertEquals(
            $this->sortedTopScoresArray,
            $iteratedTopScores
        );
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(
            6,
            count($this->topScores)
        );
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->sortedTopScoresArray,
            $this->topScores->toArray()
        );
    }
}
