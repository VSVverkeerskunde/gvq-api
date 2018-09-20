<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Doctrine\ORM\NonUniqueResultException;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
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
     * @inheritdoc
     */
    public function saveWhenHigher(DetailedTopScore $detailedTopScore): void
    {
        $foundDetailedTopScore = $this->getByDetail($detailedTopScore);

        if ($foundDetailedTopScore &&
            $foundDetailedTopScore->getScore()->toNative() >= $detailedTopScore->getScore()->toNative()) {
            return;
        }

        $this->entityManager->merge(
            DetailedTopScoreEntity::fromDetailedTopScore($detailedTopScore)
        );
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     * @throws NonUniqueResultException
     */
    public function getAverageByChannel(QuizChannel $channel): Average
    {
        $scalarScore = $this->entityManager->createQueryBuilder()
            ->select('avg(detailedTopScore.score)')
            ->from(DetailedTopScoreEntity::class, 'detailedTopScore')
            ->andWhere('detailedTopScore.channel = :channel')
            ->setParameter('channel', $channel->toNative())
            ->getQuery()
            ->getSingleScalarResult();

        $score = null === $scalarScore ? 0 : floatval($scalarScore);

        return new Average($score);
    }

    /**
     * @inheritdoc
     * @throws NonUniqueResultException
     */
    public function getAverageByLanguage(Language $language): Average
    {
        $scalarScore = $this->entityManager->createQueryBuilder()
            ->select('avg(detailedTopScore.score)')
            ->from(DetailedTopScoreEntity::class, 'detailedTopScore')
            ->where('detailedTopScore.language = :language')
            ->setParameter('language', $language->toNative())
            ->getQuery()
            ->getSingleScalarResult();

        $score = null === $scalarScore ? 0 : floatval($scalarScore);

        return new Average($score);
    }

    /**
     * @inheritdoc
     * @throws NonUniqueResultException
     */
    public function getQuizAverage(?Language $language): Average
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('avg(detailedTopScore.score)')
            ->from(DetailedTopScoreEntity::class, 'detailedTopScore')
            ->where('detailedTopScore.channel != :channel')
            ->setParameter('channel', QuizChannel::CUP);

        if ($language !== null) {
            $queryBuilder = $queryBuilder
                ->andWhere('detailedTopScore.language = :language')
                ->setParameter('language', $language->toNative());
        }

        $scalarScore = $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();

        $score = null === $scalarScore ? 0 : floatval($scalarScore);

        return new Average($score);
    }

    /**
     * @inheritdoc
     * @throws NonUniqueResultException
     */
    public function getTotalAverage(): Average
    {
        $scalarScore = $this->entityManager->createQueryBuilder()
            ->select('avg(detailedTopScore.score)')
            ->from(DetailedTopScoreEntity::class, 'detailedTopScore')
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
