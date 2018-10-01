<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\Statistics\ValueObjects\Percentage;

class QuestionDifficultiesTest extends TestCase
{
    /**
     * @var QuestionDifficulty[]
     */
    private $questionDifficultiesAsArray;

    /**
     * @var QuestionDifficulties
     */
    private $questionDifficulties;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->questionDifficultiesAsArray = [
            new QuestionDifficulty(
                ModelsFactory::createAccidentQuestion(),
                new Percentage(0.66)
            ),
            new QuestionDifficulty(
                ModelsFactory::createGeneralQuestion(),
                new Percentage(0.77)
            )
        ];

        $this->questionDifficulties = new QuestionDifficulties(
            ...$this->questionDifficultiesAsArray
        );
    }

    /**
     * @test
     */
    public function it_can_be_iterated(): void
    {
        $questionDifficultiesAsArray = [];
        foreach ($this->questionDifficulties as $questionDifficulty) {
            $questionDifficultiesAsArray[] = $questionDifficulty;
        }

        $this->assertEquals(
            $this->questionDifficultiesAsArray,
            $questionDifficultiesAsArray
        );
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(
            2,
            count($this->questionDifficulties)
        );
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_array(): void
    {
        $this->assertEquals(
            $this->questionDifficultiesAsArray,
            $this->questionDifficulties->toArray()
        );
    }
}
