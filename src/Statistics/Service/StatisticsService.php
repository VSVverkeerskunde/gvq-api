<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Service;

use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\ValueObjects\EmployeeParticipationRatio;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\CountableRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StatisticsService
{
    /**
     * @var StartedQuizRepository
     */
    private $startedQuizRepository;

    /**
     * @var FinishedQuizRepository
     */
    private $finishedQuizRepository;

    /**
     * @var UniqueParticipantRepository
     */
    private $uniqueParticipantRepository;

    /**
     * @var PartnerRepository
     */
    private $partnerRepository;

    /**
     * @var StatisticsKey[]
     */
    private $statisticsKeys;

    /**
     * @var CompanyRepository
     */
    private $companies;

    /**
     * @var EmployeeParticipationRepository
     */
    private $employeeParticipations;

    /**
     * @param StartedQuizRepository $startedQuizRepository
     * @param FinishedQuizRepository $finishedQuizRepository
     * @param UniqueParticipantRepository $uniqueParticipantRepository
     * @param PartnerRepository $partnerRepository
     * @param CompanyRepository $companies
     * @param EmployeeParticipationRepository $employeeParticipations
     */
    public function __construct(
        StartedQuizRepository $startedQuizRepository,
        FinishedQuizRepository $finishedQuizRepository,
        UniqueParticipantRepository $uniqueParticipantRepository,
        PartnerRepository $partnerRepository,
        CompanyRepository $companies,
        EmployeeParticipationRepository $employeeParticipations
    ) {
        $this->startedQuizRepository = $startedQuizRepository;
        $this->finishedQuizRepository = $finishedQuizRepository;
        $this->uniqueParticipantRepository = $uniqueParticipantRepository;
        $this->partnerRepository = $partnerRepository;

        $this->statisticsKeys = StatisticsKey::getAllKeys();
        $this->companies = $companies;
        $this->employeeParticipations = $employeeParticipations;
    }

    /**
     * @return int[]
     */
    public function getStartedQuizCounts(): array
    {
        return $this->getCountsFromRepository($this->startedQuizRepository);
    }

    /**
     * @return int[]
     */
    public function getFinishedQuizCounts(): array
    {
        return $this->getCountsFromRepository($this->finishedQuizRepository);
    }

    /**
     * @return int[]
     */
    public function getUniqueParticipantCounts(): array
    {
        return $this->getCountsFromRepository($this->uniqueParticipantRepository);
    }

    /**
     * @param Year $year
     * @return array|null
     */
    public function getUniqueParticipantCountsForPartnersByYear(Year $year): ?array
    {
        $partners = $this->partnerRepository->getAllByYear($year);

        if (empty($partners)) {
            return null;
        }

        $counts = [];

        foreach ($partners as $partner) {
            /** @var Partner $partner */
            $nlCount = $this->uniqueParticipantRepository->getCountForPartner(
                new StatisticsKey(StatisticsKey::PARTNER_NL),
                $partner
            );

            $frCount = $this->uniqueParticipantRepository->getCountForPartner(
                new StatisticsKey(StatisticsKey::PARTNER_FR),
                $partner
            );

            $totalCount = $nlCount + $frCount;

            $counts[$partner->getName()->toNative()][Language::NL] = $nlCount;
            $counts[$partner->getName()->toNative()][Language::FR] = $frCount;
            $counts[$partner->getName()->toNative()]['total'] = $totalCount;
        }

        return $counts;
    }

    /**
     * @param UuidInterface $companyId
     * @return EmployeeParticipationRatio
     * @throws InvalidArgumentException
     */
    public function getEmployeeParticipationRatio(UuidInterface $companyId): EmployeeParticipationRatio
    {
        $company = $this->companies->getById($companyId);

        if (null === $company) {
            throw new InvalidArgumentException('Unknown company');
        }

        return new EmployeeParticipationRatio(
            $this->employeeParticipations->countByCompany($companyId),
            $company->getNumberOfEmployees()
        );
    }

    /**
     * @param CountableRepository $statisticsRepository
     * @return array
     */
    private function getCountsFromRepository(CountableRepository $statisticsRepository): array
    {
        $totalNL = 0;
        $totalFR = 0;
        $counts = [];

        foreach ($this->statisticsKeys as $statisticsKey) {
            $key = $statisticsKey->toNative();

            $counts[$key] = $statisticsRepository->getCount($statisticsKey);

            if ($statisticsKey->getLanguage()->toNative() === Language::NL) {
                $totalNL += $counts[$key];
            } else {
                $totalFR += $counts[$key];
            }

            $totalKey = substr($key, 0, -2).'total';

            if (key_exists($totalKey, $counts)) {
                $counts[$totalKey] += $counts[$key];
            } else {
                $counts[$totalKey] = $counts[$key];
            }
        }

        $counts['quiz_total_nl'] = $totalNL - $counts['cup_nl'];
        $counts['quiz_total_fr'] = $totalFR - $counts['cup_fr'];
        $counts['quiz_total'] = $counts['quiz_total_nl'] + $counts['quiz_total_fr'];
        $counts['total_nl'] = $totalNL;
        $counts['total_fr'] = $totalFR;
        $counts['total'] = $totalNL + $totalFR;


        return $counts;
    }
}
