<?php
/**
 * @file
 */

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Statistics\Models\TeamParticipant;
use VSV\GVQ_API\Statistics\Repositories\Entities\TeamParticipantEntity;

class TeamParticipantDoctrineRepository extends AbstractDoctrineRepository implements TeamParticipantRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return TeamParticipantEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function save(TeamParticipant $teamParticipant): void
    {
        /** @var TeamParticipantEntity $teamParticipantEntity */
        $teamParticipantEntity = $this->objectRepository->findOneBy(
            [
                'email' => $teamParticipant->getEmail()->toNative(),
                'teamId' => $teamParticipant->getTeamId()->toString(),
            ]
        );

        if ($teamParticipantEntity) {
            return;
        }

        $this->entityManager->persist(TeamParticipantEntity::fromTeamParticipant($teamParticipant));
        $this->entityManager->flush();
    }
}