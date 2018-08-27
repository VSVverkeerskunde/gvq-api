<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class ContestParticipationTest extends TestCase
{
    /**
     * @var ContestParticipation
     */
    private $contestParticipation;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        $this->contestParticipation = ModelsFactory::createQuizContestParticipation();
    }

    /**
     * @test
     */
    public function it_stores_a_year(): void
    {
        $this->assertEquals(
            new Year(2018),
            $this->contestParticipation->getYear()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_channel(): void
    {
        $this->assertEquals(
            new QuizChannel(QuizChannel::INDIVIDUAL),
            $this->contestParticipation->getChannel()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_stores_a_contest_participant(): void
    {
        $this->assertEquals(
            ModelsFactory::createContestParticipant(),
            $this->contestParticipation->getContestParticipant()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_address(): void
    {
        $this->assertEquals(
            ModelsFactory::createVsvAddress(),
            $this->contestParticipation->getAddress()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_answer_to_first_tie_breaker(): void
    {
        $this->assertEquals(
            new PositiveNumber(12345),
            $this->contestParticipation->getAnswer1()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_answer_to_second_tie_breaker(): void
    {
        $this->assertEquals(
            new PositiveNumber(54321),
            $this->contestParticipation->getAnswer2()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_throws_on_invalid_quiz_channel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid value "company" for quiz channel. Allowed values are individual and cup.'
        );

        new ContestParticipation(
            new Year(2018),
            new QuizChannel(QuizChannel::COMPANY),
            ModelsFactory::createContestParticipant(),
            ModelsFactory::createVsvAddress(),
            new PositiveNumber(12345),
            new PositiveNumber(54321)
        );
    }
}
