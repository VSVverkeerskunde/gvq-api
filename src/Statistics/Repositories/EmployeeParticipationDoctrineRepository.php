<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\Statistics\Repositories\Entities\DetailedTopScoreEntity;
use VSV\GVQ_API\Statistics\Repositories\Entities\EmployeeParticipationEntity;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

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

    public function countByCompanyAndLanguage(
        UuidInterface $companyId,
        Language $language
    ): int {
        $qb = $this->entityManager->createQueryBuilder();

        // select count(distinct t.email) from detailed_top_score t inner join employee_participation e ON (t.email=e.email) where e.company_id='01eb0ee6-ee25-4bb5-8b4e-a6b7213b548a' and t.language=‘nl'
        $qb->select('count(distinct participation.email)')
            ->from($this->getRepositoryName(), 'participation')
            ->innerJoin(DetailedTopScoreEntity::class, 'score', Join::WITH, 'score.email = participation.email AND score.language = :language')
            ->where($qb->expr()->eq('participation.companyId', ':companyId'))
            ->setParameter('companyId', $companyId->toString())
            ->setParameter('language', $language->toNative());

        $result = $qb->getQuery()->getSingleScalarResult();
        return (int) $result;
    }

    public function getByEmail(Email $email): iterable
    {
        $participations = $this->objectRepository->findBy(
            [
                'email' => $email->toNative(),
            ]
        );

        foreach ($participations as $participation) {
            yield $participation->toEmployeeParticipation();
        }
    }
}
