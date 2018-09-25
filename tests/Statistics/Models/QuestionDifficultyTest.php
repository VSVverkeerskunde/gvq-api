<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class QuestionDifficultyTest extends TestCase
{
    /**
     * @var QuestionDifficulty
     */
    private $questionDifficulty;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->questionDifficulty = new QuestionDifficulty(
            ModelsFactory::createGeneralQuestion(),
            new NaturalNumber(2)
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_stores_a_question(): void
    {
        $this->assertEquals(
            ModelsFactory::createGeneralQuestion(),
            $this->questionDifficulty->getQuestion()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_stores_a_count(): void
    {
        $this->assertEquals(
            new NaturalNumber(2),
            $this->questionDifficulty->getCount()
        );
    }
}
