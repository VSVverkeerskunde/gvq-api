<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Repositories\Entities\EmployeeParticipationEntity;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class EmployeeParticipationDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var EmployeeParticipationDoctrineRepository
     */
    private $employeeParticipationDoctrineRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeeParticipationDoctrineRepository = new EmployeeParticipationDoctrineRepository(
            $this->entityManager
        );

        $employeeParticipations = ModelsFactory::createEmployeeParticipations();

        foreach ($employeeParticipations as $employeeParticipation) {
            $this->employeeParticipationDoctrineRepository->save($employeeParticipation);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return EmployeeParticipationEntity::class;
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_count_the_participations_by_company(): void
    {
        $companyCount = $this->employeeParticipationDoctrineRepository->countByCompany(
            Uuid::fromString('da5f2e1f-43c9-4ffc-90c1-761c2bc2453e')
        );

        $this->assertEquals(
            new NaturalNumber(2),
            $companyCount
        );

        $companyCount = $this->employeeParticipationDoctrineRepository->countByCompany(
            Uuid::fromString('6e25425c-77cd-4899-9bfd-c2b8defb339f')
        );

        $this->assertEquals(
            new NaturalNumber(2),
            $companyCount
        );
    }
}
