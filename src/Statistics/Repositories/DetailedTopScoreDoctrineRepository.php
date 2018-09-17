<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Doctrine\ORM\NonUniqueResultException;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\Models\DetailedTopScore;
use VSV\GVQ_API\Statistics\Repositories\Entities\DetailedTopScoreEntity;
use VSV\GVQ_API\Statistics\ValueObjects\Average;

class DetailedTopScoreDoctrineRepository extends AbstractDoctrineRepository implements
    DetailedTopScoreRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return DetailedTopScoreEntity::class;
    }

    /**
     * @param DetailedTopScore $detailedTopScore
     */
    public function saveWhenHigher(DetailedTopScore $detailedTopScore): void
    {
        $foundDetailedTopScore = $this->getByDetail($detailedTopScore);

        if ($foundDetailedTopScore &&
            $foundDetailedTopScore >= $detailedTopScore->getScore()->toNative()) {
            return;
        }

        $this->entityManager->merge(
            DetailedTopScoreEntity::fromDetailedTopScore($detailedTopScore)
        );
        $this->entityManager->flush();
    }

    /**
     * @param StatisticsKey $statisticsKey
     * @return Average
     * @throws NonUniqueResultException
     */
    public function getAverageByKey(StatisticsKey $statisticsKey): Average
    {
        $scalarScore = $this->entityManager->createQueryBuilder()
            ->select('avg(detailedTopScore.score)')
            ->from(DetailedTopScoreEntity::class, 'detailedTopScore')
            ->where('detailedTopScore.language = :language')
            ->andWhere('detailedTopScore.channel = :channel')
            ->setParameter('language', $statisticsKey->getLanguage()->toNative())
            ->setParameter('channel', $statisticsKey->getChannel()->toNative())
            ->getQuery()
            ->getSingleScalarResult();

        $score = null === $scalarScore ? 0 : floatval($scalarScore);

        return new Average($score);
    }

    /**
     * @param DetailedTopScore $detailedTopScore
     * @return DetailedTopScore
     */
    private function getByDetail(DetailedTopScore $detailedTopScore): ?DetailedTopScore
    {
        /** @var DetailedTopScoreEntity $detailedTopScoreEntity */
        $detailedTopScoreEntity = $this->objectRepository->findOneBy(
            [
                'email' => $detailedTopScore->getEmail()->toNative(),
                'language' => $detailedTopScore->getLanguage()->toNative(),
                'channel' => $detailedTopScore->getChannel()->toNative(),
            ]
        );

        return $detailedTopScoreEntity ? $detailedTopScoreEntity->toDetailedTopScore() : null;
    }
}
