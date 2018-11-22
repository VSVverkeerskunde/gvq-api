<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use function array_chunk;
use function array_slice;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Company\Repositories\Entities\CompanyEntity;
use VSV\GVQ_API\Statistics\Models\TopScores;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\Repositories\Entities\EmployeeParticipationEntity;
use VSV\GVQ_API\Statistics\Repositories\Entities\TopScoreEntity;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScoreDoctrineRepository extends AbstractDoctrineRepository implements TopScoreRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return TopScoreEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function saveWhenHigher(TopScore $topScore): void
    {
        $foundTopScore = $this->getByEmail($topScore->getEmail());

        if ($foundTopScore && $foundTopScore->getScore()->toNative() >= $topScore->getScore()->toNative()) {
            return;
        }

        $this->entityManager->merge(TopScoreEntity::fromTopScore($topScore));
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getByEmail(Email $email): ?TopScore
    {
        /** @var TopScoreEntity|null $topScoreEntity */
        $topScoreEntity = $this->objectRepository->findOneBy(
            [
                'email' => $email->toNative(),
            ]
        );

        return $topScoreEntity ? $topScoreEntity->toTopScore() : null;
    }

    /**
     * @inheritdoc
     */
    public function getAllByCompany(UuidInterface $companyId): TopScores
    {
        /** @var TopScoreEntity[] $topScoreEntities */
        $topScoreEntities = $this->entityManager->createQueryBuilder()
            ->select('topScore')
            ->from(EmployeeParticipationEntity::class, 'employee')
            ->where('employee.companyId = :companyId')
            ->setParameter('companyId', $companyId->toString())
            ->innerJoin(TopScoreEntity::class, 'topScore', Join::WITH, 'employee.email = topScore.email')
            ->getQuery()
            ->getResult();

        $topScores = [];
        foreach ($topScoreEntities as $topScoreEntity) {
            $topScores[] = $topScoreEntity->toTopScore();
        }

        return new TopScores(...$topScores);
    }

    /**
     * @inheritdoc
     * @throws NonUniqueResultException
     */
    public function getAverage(): Average
    {
        $scalarScore = $this->entityManager->createQueryBuilder()
            ->select('avg(topScore.score)')
            ->from(TopScoreEntity::class, 'topScore')
            ->getQuery()
            ->getSingleScalarResult();

        $score = null === $scalarScore ? 0 : floatval($scalarScore);

        return new Average($score);
    }

    /**
     * @inheritdoc
     * @throws NonUniqueResultException
     */
    public function getAverageForCompany(UuidInterface $companyId): Average
    {
        $scalarScore = $this->entityManager->createQueryBuilder()
            ->select('avg(topScore.score)')
            ->from(EmployeeParticipationEntity::class, 'employee')
            ->where('employee.companyId = :companyId')
            ->setParameter('companyId', $companyId->toString())
            ->innerJoin(TopScoreEntity::class, 'topScore', Join::WITH, 'employee.email = topScore.email')
            ->getQuery()
            ->getSingleScalarResult();

        $score = null === $scalarScore ? 0 : floatval($scalarScore);

        return new Average($score);
    }

    /**
     * @inheritdoc
     */
    public function getTopCompanies(NaturalNumber $nrOfPassedEmployees): iterable
    {
        $batchSize = 10;
        $firstResult = 0;

        $minimalScoreToSucceed = 11;

        do {
            $queryBuilder = $this->entityManager->createQueryBuilder();

            $query = $queryBuilder->select('c')
                ->from(
                    CompanyEntity::class,
                    'c'
                )
                ->setMaxResults($batchSize)
                ->setFirstResult($firstResult)
                ->getQuery();

            $currentBatchItemCount = 0;

            foreach ($query->iterate() as $companyEntities) {
                $currentBatchItemCount++;
                /** @var CompanyEntity $companyEntity */
                $companyEntity = $companyEntities[0];

                $qb = $this->entityManager->createQueryBuilder();
                $succeededEmployeesCount = $qb->select('count(employee.companyId) AS nrOfPassedEmployees')
                    ->from(EmployeeParticipationEntity::class, 'employee')
                    ->innerJoin(TopScoreEntity::class, 'topScore', Join::WITH, 'employee.email = topScore.email')
                    ->where($qb->expr()->gte('topScore.score', ':minimal_score_to_succeed'))
                    ->andWhere($qb->expr()->eq('employee.companyId', ':company_id'))
                    ->setParameter('company_id', $companyEntity->getId())
                    ->setParameter('minimal_score_to_succeed', $minimalScoreToSucceed)
                    ->getQuery()
                    ->getSingleScalarResult();

                $succeededEmployeesCount = (int) $succeededEmployeesCount;

                if ($succeededEmployeesCount < $nrOfPassedEmployees->toNative()) {
                    $this->entityManager->detach($companyEntity);
                    continue;
                }

                $company = $companyEntity->toCompany();
                $this->entityManager->detach($companyEntity);

                yield $company->withNrOfPassedEmployees(
                    new NaturalNumber(
                        $succeededEmployeesCount
                    )
                );
            }

            $firstResult += $batchSize;
        } while ($currentBatchItemCount == $batchSize);
    }
}
