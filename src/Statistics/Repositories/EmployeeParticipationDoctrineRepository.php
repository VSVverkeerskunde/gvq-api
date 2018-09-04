<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Statistics\EmployeeParticipation;
use VSV\GVQ_API\Statistics\Repositories\Entities\EmployeeParticipationEntity;

class EmployeeParticipationDoctrineRepository extends AbstractDoctrineRepository implements EmployeeParticipationRepository
{
    protected function getRepositoryName(): string
    {
        return EmployeeParticipationEntity::class;
    }

    public function save(EmployeeParticipation $employeeParticipation): void
    {
        $this->entityManager->merge(EmployeeParticipationEntity::fromEmployeeParticipation($employeeParticipation));
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $companyId
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return int
     */
    public function countParticipatingEmployeesByCompany(UuidInterface $companyId): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('count(participation.email)');
        $qb->from($this->getRepositoryName(), 'participation');

        $count = intval($qb->getQuery()->getSingleScalarResult());

        return $count;
    }
}
