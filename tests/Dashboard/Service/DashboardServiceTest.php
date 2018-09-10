<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\EmployeeParticipationRatio;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class DashboardServiceTest extends TestCase
{
    /**
     * @var CompanyRepository|MockObject
     */
    private $companyRepository;

    /**
     * @var EmployeeParticipationRepository|MockObject
     */
    private $employeeParticipationRepository;

    /**
     * @var TopScoreRepository|MockObject
     */
    private $topScoreRepository;

    /**
     * @var DashboardService
     */
    private $dashboardService;

    protected function setUp(): void
    {
        /** @var CompanyRepository|MockObject $companyRepository */
        $companyRepository = $this->createMock(CompanyRepository::class);
        $this->companyRepository = $companyRepository;

        /** @var EmployeeParticipationRepository|MockObject $employeeParticipationRepository */
        $employeeParticipationRepository = $this->createMock(EmployeeParticipationRepository::class);
        $this->employeeParticipationRepository = $employeeParticipationRepository;

        /** @var TopScoreRepository|MockObject $topScoreRepository */
        $topScoreRepository = $this->createMock(TopScoreRepository::class);
        $this->topScoreRepository = $topScoreRepository;

        $this->dashboardService = new DashboardService(
            $this->companyRepository,
            $this->employeeParticipationRepository,
            $this->topScoreRepository
        );
    }

    /**
     * @test
     */
    public function it_should_return_the_employee_participation_ratio_for_a_company(): void
    {
        $company = ModelsFactory::createCompany();

        $this->companyRepository
            ->expects($this->once())
            ->method('getById')
            ->with($company->getId())
            ->willReturn($company);

        $this->employeeParticipationRepository
            ->expects($this->once())
            ->method('countByCompany')
            ->with($company->getId())
            ->willReturn(new NaturalNumber(3));

        $participationShare = $this->dashboardService->getEmployeeParticipationRatio($company->getId());
        $expectedParticipationShare = new EmployeeParticipationRatio(
            new NaturalNumber(3),
            new PositiveNumber(49)
        );

        $this->assertEquals($expectedParticipationShare, $participationShare);
    }

    /**
     * @test
     */
    public function it_throws_for_unknown_company_when_getting_ratio(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown company');

        $company = ModelsFactory::createCompany();

        $this->companyRepository
            ->expects($this->once())
            ->method('getById')
            ->with($company->getId())
            ->willReturn(null);

        $this->dashboardService->getEmployeeParticipationRatio($company->getId());
    }

    /**
     * @test
     */
    public function it_returns_the_employee_average_top_score(): void
    {
        $company = ModelsFactory::createCompany();

        $this->companyRepository
            ->expects($this->once())
            ->method('getById')
            ->with($company->getId())
            ->willReturn($company);

        $this->topScoreRepository->expects($this->once())
            ->method('getAverageForCompany')
            ->with($company->getId())
            ->willReturn(new Average(10));

        $average = $this->dashboardService->getAverageEmployeeTopScore($company->getId());

        $this->assertEquals(
            new Average(10),
            $average
        );
    }

    /**
     * @test
     */
    public function it_throws_for_unknown_company_when_getting_employee_average(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown company');

        $company = ModelsFactory::createCompany();

        $this->companyRepository
            ->expects($this->once())
            ->method('getById')
            ->with($company->getId())
            ->willReturn(null);

        $this->dashboardService->getAverageEmployeeTopScore($company->getId());
    }

    /**
     * @test
     */
    public function it_gets_the_overall_average_top_score(): void
    {
        $this->topScoreRepository->expects($this->once())
            ->method('getAverage')
            ->willReturn(new Average(10));

        $this->assertEquals(
            new Average(10),
            $this->dashboardService->getAverageTopScore()
        );
    }
}
