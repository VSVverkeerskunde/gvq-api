<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partners;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\Repositories\DetailedTopScoreRepository;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\CountableRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

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

    /**
     * @var DetailedTopScoreRepository|MockObject
     */
    private $detailedTopScoreRepository;

    /**
     * @var TopScoreRepository|MockObject
     */
    private $topScoreRepository;

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

        /** @var DetailedTopScoreRepository|MockObject $detailedTopScoreRepository */
        $detailedTopScoreRepository = $this->createMock(DetailedTopScoreRepository::class);
        $this->detailedTopScoreRepository = $detailedTopScoreRepository;

        /** @var TopScoreRepository|MockObject $opScoreRepository */
        $opScoreRepository = $this->createMock(TopScoreRepository::class);
        $this->topScoreRepository = $opScoreRepository;

        $this->statisticsService = new StatisticsService(
            $this->startedQuizRepository,
            $this->finishedQuizRepository,
            $this->uniqueParticipantRepository,
            $this->partnerRepository,
            $this->detailedTopScoreRepository,
            $this->topScoreRepository
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
        $this->mockUniqueGetCountMethod($this->uniqueParticipantRepository);

        $counts = $this->statisticsService->getUniqueParticipantCounts();

        $this->checkUniqueCounts($counts);
    }

    /**
     * @test
     */
    public function it_can_get_unique_passed_participant_counts(): void
    {
        $this->mockUniqueGetCountMethod($this->uniqueParticipantRepository, 'getPassedCount');

        $counts = $this->statisticsService->getPassedUniqueParticipantCounts();

        $this->checkUniqueCounts($counts);
    }

    /**
     * @test
     */
    public function it_can_get_unique_passed_participant_percentage(): void
    {
        $this->uniqueParticipantRepository
            ->expects($this->exactly(18))
            ->method('getCount')
            ->withConsecutive(
                new StatisticsKey('individual_nl'),
                new StatisticsKey('individual_fr'),
                new StatisticsKey('individual_total'),
                new StatisticsKey('partner_nl'),
                new StatisticsKey('partner_fr'),
                new StatisticsKey('partner_total'),
                new StatisticsKey('company_nl'),
                new StatisticsKey('company_fr'),
                new StatisticsKey('company_total'),
                new StatisticsKey('quiz_total_nl'),
                new StatisticsKey('quiz_total_fr'),
                new StatisticsKey('quiz_total'),
                new StatisticsKey('cup_nl'),
                new StatisticsKey('cup_fr'),
                new StatisticsKey('cup_total'),
                new StatisticsKey('total_nl'),
                new StatisticsKey('total_fr'),
                new StatisticsKey('total')
            )
            ->willReturnOnConsecutiveCalls(
                0,
                4,
                6,
                8,
                10,
                12,
                14,
                16,
                18,
                20,
                22,
                24,
                26,
                28,
                30,
                32,
                34,
                36
            );

        $this->uniqueParticipantRepository
            ->expects($this->exactly(18))
            ->method('getPassedCount')
            ->withConsecutive(
                new StatisticsKey('individual_nl'),
                new StatisticsKey('individual_fr'),
                new StatisticsKey('individual_total'),
                new StatisticsKey('partner_nl'),
                new StatisticsKey('partner_fr'),
                new StatisticsKey('partner_total'),
                new StatisticsKey('company_nl'),
                new StatisticsKey('company_fr'),
                new StatisticsKey('company_total'),
                new StatisticsKey('quiz_total_nl'),
                new StatisticsKey('quiz_total_fr'),
                new StatisticsKey('quiz_total'),
                new StatisticsKey('cup_nl'),
                new StatisticsKey('cup_fr'),
                new StatisticsKey('cup_total'),
                new StatisticsKey('total_nl'),
                new StatisticsKey('total_fr'),
                new StatisticsKey('total')
            )
            ->willReturnOnConsecutiveCalls(
                0,
                0,
                0,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15,
                16,
                17,
                18
            );

        $percentages = $this->statisticsService->getPassedUniqueParticipantPercentages();

        $this->assertEquals(
            [
                'individual_nl' => 0.0,
                'individual_total' => 0.0,
                'individual_fr' => 0.0,
                'partner_nl' => 50.0,
                'partner_total' => 50.0,
                'partner_fr' => 50.0,
                'company_nl' => 50.0,
                'company_total' => 50.0,
                'company_fr' => 50.0,
                'cup_nl' => 50.0,
                'cup_total' => 50.0,
                'cup_fr' => 50.0,
                'quiz_total_nl' => 50.0,
                'quiz_total_fr' => 50.0,
                'quiz_total' => 50.0,
                'total_nl' => 50.0,
                'total_fr' => 50.0,
                'total' => 50.0,
            ],
            $percentages
        );
    }

    /**
     * @test
     */
    public function it_can_get_detailed_top_score_averages(): void
    {
        $this->detailedTopScoreRepository
            ->expects($this->exactly(8))
            ->method('getAverageByKey')
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
            ->willReturnOnConsecutiveCalls(
                new Average(10),
                new Average(12),
                new Average(10),
                new Average(12),
                new Average(10),
                new Average(12),
                new Average(10),
                new Average(12)
            );

        $this->detailedTopScoreRepository
            ->expects($this->exactly(4))
            ->method('getAverageByChannel')
            ->withConsecutive(
                new QuizChannel(QuizChannel::INDIVIDUAL),
                new QuizChannel(QuizChannel::COMPANY),
                new QuizChannel(QuizChannel::PARTNER),
                new QuizChannel(QuizChannel::CUP)
            )
            ->willReturnOnConsecutiveCalls(
                new Average(11),
                new Average(11),
                new Average(11),
                new Average(11)
            );

        $this->detailedTopScoreRepository
            ->expects($this->exactly(3))
            ->method('getQuizAverage')
            ->withConsecutive(
                [
                    new Language(Language::NL),
                ],
                [
                    new Language(Language::FR),
                ],
                [
                    null,
                ]
            )
            ->willReturnOnConsecutiveCalls(
                new Average(11),
                new Average(12),
                new Average(11.5)
            );

        $this->detailedTopScoreRepository
            ->expects($this->exactly(2))
            ->method('getAverageByLanguage')
            ->withConsecutive(
                new Language(Language::NL),
                new Language(Language::FR)
            )
            ->willReturnOnConsecutiveCalls(
                new Average(11),
                new Average(12)
            );

        $this->detailedTopScoreRepository
            ->expects($this->once())
            ->method('getTotalAverage')
            ->willReturn(new Average(11.5));

        $averages = $this->statisticsService->getDetailedTopScoreAverages();

        $this->assertEquals(
            [
                'individual_nl' => 10.0,
                'individual_fr' => 12.0,
                'individual_total' => 11.0,
                'partner_nl' => 10.0,
                'partner_fr' => 12.0,
                'partner_total' => 11.0,
                'company_nl' => 10.0,
                'company_fr' => 12.0,
                'company_total' => 11.0,
                'cup_nl' => 10.0,
                'cup_fr' => 12.0,
                'cup_total' => 11.0,
                'quiz_total_nl' => 11.0,
                'quiz_total_fr' => 12.0,
                'quiz_total' => 11.5,
                'total_nl' => 11.0,
                'total_fr' => 12.0,
                'total' => 11.5,
            ],
            $averages
        );
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
            ->method('getCountForPartner')
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

        $counts = $this->statisticsService->getUniqueParticipantCountsForPartnersByYear(new Year(2018));


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
            $this->statisticsService->getUniqueParticipantCountsForPartnersByYear(new Year(2018))
        );
    }

    /**
     * @test
     */
    public function it_can_get_the_top_score_companies(): void
    {
        $this->topScoreRepository->expects($this->once())
            ->method('getTopCompanies')
            ->with(new NaturalNumber(10))
            ->willReturn(
                new Companies(ModelsFactory::createCompany())
            );

        $topScoreCompanies = $this->statisticsService->getTopCompanies();

        $this->assertEquals(
            new Companies(ModelsFactory::createCompany()),
            $topScoreCompanies
        );
    }

    /**
     * @param CountableRepository|MockObject $statisticsRepository
     * @param string $method
     */
    private function mockGetCountMethod(
        MockObject $statisticsRepository,
        string $method = 'getCount'
    ): void {
        $statisticsRepository
            ->expects($this->exactly(8))
            ->method($method)
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
     * @param CountableRepository|MockObject $statisticsRepository
     * @param string $method
     */
    private function mockUniqueGetCountMethod(
        MockObject $statisticsRepository,
        string $method = 'getCount'
    ): void {
        $statisticsRepository
            ->expects($this->exactly(18))
            ->method($method)
            ->withConsecutive(
                new StatisticsKey('individual_nl'),
                new StatisticsKey('individual_fr'),
                new StatisticsKey('individual_total'),
                new StatisticsKey('partner_nl'),
                new StatisticsKey('partner_fr'),
                new StatisticsKey('partner_total'),
                new StatisticsKey('company_nl'),
                new StatisticsKey('company_fr'),
                new StatisticsKey('company_total'),
                new StatisticsKey('quiz_total_nl'),
                new StatisticsKey('quiz_total_fr'),
                new StatisticsKey('quiz_total'),
                new StatisticsKey('cup_nl'),
                new StatisticsKey('cup_fr'),
                new StatisticsKey('cup_total'),
                new StatisticsKey('total_nl'),
                new StatisticsKey('total_fr'),
                new StatisticsKey('total')
            )
            ->willReturnOnConsecutiveCalls(
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15,
                16,
                17,
                18
            );
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

    /**
     * @param array $counts
     */
    private function checkUniqueCounts(array $counts): void
    {
        $this->assertArraySubset(
            [
                'individual_nl' => 1,
                'individual_fr' => 2,
                'individual_total' => 3,
                'partner_nl' => 4,
                'partner_fr' => 5,
                'partner_total' => 6,
                'company_nl' => 7,
                'company_fr' => 8,
                'company_total' => 9,
                'quiz_total_nl' => 10,
                'quiz_total_fr' => 11,
                'quiz_total' => 12,
                'cup_nl' => 13,
                'cup_fr' => 14,
                'cup_total' => 15,
                'total_nl' => 16,
                'total_fr' => 17,
                'total' => 18,
            ],
            $counts
        );
    }
}
