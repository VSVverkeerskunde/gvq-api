<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Contest\Repositories\Entities\ContestParticipationEntity;

class ContestParticipationDoctrineRepository extends AbstractDoctrineRepository implements
    ContestParticipationRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return ContestParticipationEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function save(ContestParticipation $contestParticipation): void
    {
        $contestParticipantEntity = ContestParticipationEntity::fromContestParticipation($contestParticipation);

        // The user object inside company is not managed,
        // therefore we need to use merge instead of persist.
        // When user wouldn't exist yet, the user is not created.
        $this->entityManager->merge($contestParticipantEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getAll(): ?ContestParticipations
    {
        /** @var ContestParticipation[] $contestParticipations */
        $contestParticipations = $this->objectRepository->findAll();

        return $this->toContestParticipations($contestParticipations);
    }

    /**
     * @param ContestParticipation[] $contestParticipations
     * @return null|ContestParticipations
     */
    private function toContestParticipations(array $contestParticipations): ?ContestParticipations
    {
        if (empty($contestParticipations)) {
            return null;
        }

        return new ContestParticipations(
            ...array_map(
                function (ContestParticipationEntity $contestParticipationEntity) {
                    return $contestParticipationEntity->toContestParticipation();
                },
                $contestParticipations
            )
        );
    }
}
