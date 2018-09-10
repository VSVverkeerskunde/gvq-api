<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\AverageScore;
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
     * @throws NonUniqueResultException
     */
    public function getAverageScoreForCompany(UuidInterface $companyId): AverageScore
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

        return new AverageScore($companyId, new Average($score));
    }
}
