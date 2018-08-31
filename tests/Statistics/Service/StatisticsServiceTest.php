<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partners;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\CountableRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
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

    /**
     * @var UniqueParticipantRepository|MockObject
     */
    private $uniqueParticipantRepository;

    /**
     * @var PartnerRepository|MockObject
     */
    private $partnerRepository;

    protected function setUp(): void
    {
        /** @var StartedQuizRepository|MockObject $startedQuizRepository */
        $startedQuizRepository = $this->createMock(StartedQuizRepository::class);
        $this->startedQuizRepository = $startedQuizRepository;

        /** @var FinishedQuizRepository|MockObject $finishedQuizRepository */
        $finishedQuizRepository = $this->createMock(FinishedQuizRepository::class);
        $this->finishedQuizRepository = $finishedQuizRepository;

        /** @var UniqueParticipantRepository|MockObject $uniqueParticipantRepository */
        $uniqueParticipantRepository = $this->createMock(UniqueParticipantRepository::class);
        $this->uniqueParticipantRepository = $uniqueParticipantRepository;

        /** @var PartnerRepository|MockObject $partnerRepository */
        $partnerRepository = $this->createMock(PartnerRepository::class);
        $this->partnerRepository = $partnerRepository;

        $this->statisticsService = new StatisticsService(
            $this->startedQuizRepository,
            $this->finishedQuizRepository,
            $this->uniqueParticipantRepository,
            $this->partnerRepository,
            new Year(2018)
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
     * @test
     */
    public function it_can_get_unique_participant_counts(): void
    {
        $this->mockGetCountMethod($this->uniqueParticipantRepository);

        $counts = $this->statisticsService->getUniqueParticipantCounts();

        $this->checkCounts($counts);
    }

    /**
     * @test
     */
    public function it_can_get_unique_participant_counts_for_partners(): void
    {
        $datsPartner = ModelsFactory::createDatsPartner();
        $nieuwsbladPartner = ModelsFactory::createNBPartner();

        $this->partnerRepository->expects($this->once())
            ->method('getAllByYear')
            ->with(new Year(2018))
            ->willReturn(
                new Partners(
                    $datsPartner,
                    $nieuwsbladPartner
                )
            );

        $this->uniqueParticipantRepository->expects($this->exactly(4))
            ->method('getPartnerCount')
            ->withConsecutive(
                [
                    new StatisticsKey(StatisticsKey::PARTNER_NL),
                    $datsPartner,
                ],
                [
                    new StatisticsKey(StatisticsKey::PARTNER_FR),
                    $datsPartner,
                ],
                [
                    new StatisticsKey(StatisticsKey::PARTNER_NL),
                    $nieuwsbladPartner,
                ],
                [
                    new StatisticsKey(StatisticsKey::PARTNER_FR),
                    $nieuwsbladPartner,
                ]
            )
            ->willReturnOnConsecutiveCalls(1, 2, 3, 4);

        $counts = $this->statisticsService->getUniqueParticipantCountsForPartners();


        $this->assertEquals(
            [
                'Dats24' =>
                    [
                        'nl' => 1,
                        'fr' => 2,
                        'total' => 3,
                    ],
                'Nieuwsblad' =>
                    [
                        'nl' => 3,
                        'fr' => 4,
                        'total' => 7,
                    ],
            ],
            $counts
        );
    }

    /**
     * @test
     */
    public function it_returns_null_for_unique_participants_when_no_partners_present(): void
    {
        $this->partnerRepository->expects($this->once())
            ->method('getAllByYear')
            ->with(new Year(2018))
            ->willReturn(null);

        $this->assertNull(
            $this->statisticsService->getUniqueParticipantCountsForPartners()
        );
    }

    /**
     * @param CountableRepository|MockObject $statisticsRepository
     */
    private function mockGetCountMethod(MockObject $statisticsRepository): void
    {
        $statisticsRepository
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
