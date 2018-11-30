<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Service;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Statistics\Models\TopScores;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\EmployeeParticipationRatio;

class DashboardService
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var EmployeeParticipationRepository
     */
    private $employeeParticipationRepository;

    /**
     * @var TopScoreRepository
     */
    private $topScoreRepository;

    /**
     * @param CompanyRepository $companyRepository
     * @param EmployeeParticipationRepository $employeeParticipationRepository
     * @param TopScoreRepository $topScoreRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        EmployeeParticipationRepository $employeeParticipationRepository,
        TopScoreRepository $topScoreRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->employeeParticipationRepository = $employeeParticipationRepository;
        $this->topScoreRepository = $topScoreRepository;
    }

    /**
     * @param UuidInterface $companyId
     * @return EmployeeParticipationRatio
     * @throws \InvalidArgumentException
     */
    public function getEmployeeParticipationRatio(UuidInterface $companyId): EmployeeParticipationRatio
    {
        $company = $this->getCompany($companyId);

        return new EmployeeParticipationRatio(
            $this->employeeParticipationRepository->countByCompany($companyId),
            $company->getNumberOfEmployees()
        );
    }

    public function uniqueParticipants(UuidInterface $companyId): array
    {
        return [
            'quiz_total_nl' => $this->employeeParticipationRepository->countByCompanyAndLanguage($companyId, new Language('nl')),
            'quiz_total_fr' => $this->employeeParticipationRepository->countByCompanyAndLanguage($companyId, new Language('fr')),
            'quiz_total' => $this->employeeParticipationRepository->countByCompany($companyId)->toNative(),
        ];
    }

    public function uniquePassedParticipants(UuidInterface $companyId): array
    {
        return [
            'quiz_total_nl' => $this->employeeParticipationRepository->countPassedByCompanyAndLanguage($companyId, new Language('nl')),
            'quiz_total_fr' => $this->employeeParticipationRepository->countPassedByCompanyAndLanguage($companyId, new Language('fr')),
            'quiz_total' => $this->employeeParticipationRepository->countPassedByCompany($companyId),
        ];
    }

    /**
     * Copied and adapted from StatisticsService::getPassedUniqueParticipantPercentages().
     *
     * @param \Ramsey\Uuid\UuidInterface $companyId
     * @return array
     */
    public function uniquePassedParticipantsPercentages(UuidInterface $companyId): array
    {
        $uniqueParticipantsCounts = $this->uniqueParticipants($companyId);
        $passedUniqueParticipantCounts = $this->uniquePassedParticipants($companyId);

        $passedUniqueParticipantPercentage = [];
        foreach ($uniqueParticipantsCounts as $key => $uniqueParticipantsCount) {
            if (empty($uniqueParticipantsCounts[$key])) {
                $passedUniqueParticipantPercentage[$key] = 0;
            } else {
                $passedUniqueParticipantPercentage[$key] = round(
                        (float)$passedUniqueParticipantCounts[$key] / (float)$uniqueParticipantsCounts[$key],
                        2
                    ) * 100;
            }
        }

        return $passedUniqueParticipantPercentage;
    }

    public function averageTopscores(UuidInterface $companyId): array
    {
        return [
            'quiz_total_nl' => $this->employeeParticipationRepository->getAverageTopScoreForCompanyAndLanguage($companyId, new Language('nl')),
            'quiz_total_fr' => $this->employeeParticipationRepository->getAverageTopScoreForCompanyAndLanguage($companyId, new Language('fr')),
            'quiz_total' => $this->topScoreRepository->getAverageForCompany($companyId)->toNative(),
        ];
    }

    /**
     * @param UuidInterface $companyId
     * @return Average
     */
    public function getAverageEmployeeTopScore(UuidInterface $companyId): Average
    {
        $company = $this->getCompany($companyId);

        return $this->topScoreRepository->getAverageForCompany(
            $company->getId()
        );
    }

    /**
     * @return Average
     */
    public function getAverageTopScore(): Average
    {
        return $this->topScoreRepository->getAverage();
    }

    /**
     * @param UuidInterface $companyId
     * @return TopScores
     */
    public function getTopScoresByCompany(UuidInterface $companyId): TopScores
    {
        return $this->topScoreRepository->getAllByCompany($companyId);
    }

    /**
     * @param UuidInterface $companyId
     * @return Company
     */
    private function getCompany(UuidInterface $companyId): Company
    {
        $company = $this->companyRepository->getById($companyId);

        if (null === $company) {
            throw new \InvalidArgumentException('Unknown company');
        }

        return $company;
    }
}
