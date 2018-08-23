<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Factory\ModelsFactory;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class TieBreakerTest extends TestCase
{
    /**
     * @var TieBreaker
     */
    private $tieBreaker;

    protected function setUp(): void
    {
        $this->tieBreaker = ModelsFactory::createTieBreaker();
    }

    /**
     * @test
     */
    public function it_stores_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('72a90e90-d54e-48f4-b29d-32e88e06b86c'),
            $this->tieBreaker->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_year(): void
    {
        $this->assertEquals(
            new Year(2018),
            $this->tieBreaker->getYear()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_channel(): void
    {
        $this->assertEquals(
            new QuizChannel(QuizChannel::INDIVIDUAL),
            $this->tieBreaker->getChannel()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_language(): void
    {
        $this->assertEquals(
            new Language('nl'),
            $this->tieBreaker->getLanguage()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_question(): void
    {
        $this->assertEquals(
            new NotEmptyString('Hoeveel van hen behaalden 11/15 of meer?'),
            $this->tieBreaker->getQuestion()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_answer(): void
    {
        $this->assertEquals(
            new PositiveNumber(14564),
            $this->tieBreaker->getAnswer()
        );
    }
}
