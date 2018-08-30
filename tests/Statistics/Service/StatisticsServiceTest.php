<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Quiz\Repositories\CounterRepository;
use VSV\GVQ_API\Quiz\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StatisticsServiceTest extends TestCase
{
    /**
     * @var StatisticsService
     */
    private $statisticsService;

    /**
     * @var StartedQuizRepository|MockObject
     */
    private $startedQuizRepository;

    /**
     * @var FinishedQuizRepository|MockObject
     */
    private $finishedQuizRepository;

    protected function setUp(): void
    {
        /** @var StartedQuizRepository|MockObject $startedQuizRepository */
        $startedQuizRepository = $this->createMock(StartedQuizRepository::class);
        $this->startedQuizRepository = $startedQuizRepository;

        /** @var FinishedQuizRepository|MockObject $finishedQuizRepository */
        $finishedQuizRepository = $this->createMock(FinishedQuizRepository::class);
        $this->finishedQuizRepository = $finishedQuizRepository;

        $this->statisticsService = new StatisticsService(
            $this->startedQuizRepository,
            $this->finishedQuizRepository
        );
    }

    /**
     * @test
     */
    public function it_can_get_started_quiz_counts(): void
    {
        $this->mockGetCountMethod($this->startedQuizRepository);

        $counts = $this->statisticsService->getStartedQuizCounts();

        $this->checkCounts($counts);
    }

    /**
     * @test
     */
    public function it_can_get_finished_quiz_counts(): void
    {
        $this->mockGetCountMethod($this->finishedQuizRepository);

        $counts = $this->statisticsService->getFinishedQuizCounts();

        $this->checkCounts($counts);
    }

    /**
     * @param CounterRepository|MockObject $counterRepository
     */
    private function mockGetCountMethod(MockObject $counterRepository): void
    {
        $counterRepository
            ->expects($this->exactly(8))
            ->method('getCount')
            ->withConsecutive(
                new StatisticsKey('individual_nl'),
                new StatisticsKey('individual_fr'),
                new StatisticsKey('partner_nl'),
                new StatisticsKey('partner_fr'),
                new StatisticsKey('company_nl'),
                new StatisticsKey('company_fr'),
                new StatisticsKey('cup_nl'),
                new StatisticsKey('cup_fr')
            )
            ->willReturnOnConsecutiveCalls(1, 2, 3, 4, 5, 6, 7, 8);
    }

    /**
     * @param array $counts
     */
    private function checkCounts(array $counts): void
    {
        $this->assertArraySubset(
            [
                'individual_nl' => 1,
                'individual_fr' => 2,
                'individual_total' => 3,
                'partner_nl' => 3,
                'partner_fr' => 4,
                'partner_total' => 7,
                'company_nl' => 5,
                'company_fr' => 6,
                'company_total' => 11,
                'quiz_total_nl' => 9,
                'quiz_total_fr' => 12,
                'quiz_total' => 21,
                'cup_nl' => 7,
                'cup_fr' => 8,
                'cup_total' => 15,
                'total_nl' => 16,
                'total_fr' => 20,
                'total' => 36,
            ],
            $counts
        );
    }
}
