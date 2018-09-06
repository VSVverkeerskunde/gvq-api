<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Doctrine\ORM\NonUniqueResultException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\Statistics\Repositories\Entities\EmployeeParticipationEntity;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class EmployeeParticipationDoctrineRepository extends AbstractDoctrineRepository implements EmployeeParticipationRepository // phpcs:ignore
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return EmployeeParticipationEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function save(EmployeeParticipation $employeeParticipation): void
    {
        $this->entityManager->persist(EmployeeParticipationEntity::fromEmployeeParticipation($employeeParticipation));
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     * @throws NonUniqueResultException
     */
    public function countByCompany(UuidInterface $companyId): NaturalNumber
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('count(participation.email)');
        $qb->from($this->getRepositoryName(), 'participation');

        $count = new NaturalNumber($qb->getQuery()->getSingleScalarResult());

        return $count;
    }
}
