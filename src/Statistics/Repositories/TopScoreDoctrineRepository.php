<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Statistics\Repositories\Entities\TopScoreEntity;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScoreDoctrineRepository extends AbstractDoctrineRepository implements TopScoreRepository
{
    protected function getRepositoryName(): string
    {
        return TopScoreEntity::class;
    }

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

    public function save(TopScore $topScore): void
    {
        $this->entityManager->merge(TopScoreEntity::fromTopScore($topScore));
        $this->entityManager->flush();
    }
}
