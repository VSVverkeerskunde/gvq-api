<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

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
    public function getTopCompanies(NaturalNumber $nrOfPassedEmployees): ?Companies
    {
        $companyIdsWithCount = $this->entityManager->createQueryBuilder()
            ->select('employee.companyId, count(employee.companyId) AS nrOfPassedEmployees')
            ->from(EmployeeParticipationEntity::class, 'employee')
            ->innerJoin(TopScoreEntity::class, 'topScore', Join::WITH, 'employee.email = topScore.email')
            ->groupBy('employee.companyId')
            ->having('count(employee.companyId) >= :nrOfPassedEmployees')
            ->where('topScore.score >= 11')
            ->setParameter('nrOfPassedEmployees', $nrOfPassedEmployees->toNative())
            ->getQuery()
            ->getResult();

        $companyIds = [];
        $nrOfPassedEmployees = [];
        foreach ($companyIdsWithCount as $companyIdWithCount) {
            $companyIds[] = $companyIdWithCount['companyId'];
            $nrOfPassedEmployees[$companyIdWithCount['companyId']] = (int)$companyIdWithCount['nrOfPassedEmployees'];
        }

        if (!empty($companyIds)) {
            /** @var CompanyEntity[] $companyEntities */
            $companyEntities = $this->entityManager->createQueryBuilder()
                ->select('c, u')
                ->from(CompanyEntity::class, 'c')
                ->innerJoin('c.translatedAliasEntities', 'a')
                ->innerJoin('c.userEntity', 'u')
                ->where('c.id IN (:ids)')
                ->setParameter('ids', $companyIds)
                ->getQuery()
                ->getResult();

            return new Companies(
                ...array_map(
                    function (CompanyEntity $companyEntity) use ($nrOfPassedEmployees) {
                        $company = $companyEntity->toCompany();
                        return $company->withNrOfPassedEmployees(
                            new NaturalNumber(
                                $nrOfPassedEmployees[$company->getId()->toString()]
                            )
                        );
                    },
                    $companyEntities
                )
            );
        } else {
            return null;
        }
    }
}
