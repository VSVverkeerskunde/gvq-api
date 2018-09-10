<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Doctrine\ORM\NonUniqueResultException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\Statistics\Repositories\Entities\EmployeeParticipationEntity;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class EmployeeParticipationDoctrineRepository extends AbstractDoctrineRepository implements
    EmployeeParticipationRepository
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
        /** @var EmployeeParticipationEntity $employeeParticipationEntity */
        $employeeParticipationEntity = $this->objectRepository->findOneBy(
            [
                'email' => $employeeParticipation->getEmail()->toNative(),
                'companyId' => $employeeParticipation->getCompanyId()->toString(),
            ]
        );

        if ($employeeParticipationEntity) {
            return;
        }

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

        $qb->select('count(participation.email)')
            ->from($this->getRepositoryName(), 'participation')
            ->where('participation.companyId = :companyId')
            ->setParameter(':companyId', $companyId->toString());

        $result = intval($qb->getQuery()->getSingleScalarResult());
        return new NaturalNumber($result);
    }
}
