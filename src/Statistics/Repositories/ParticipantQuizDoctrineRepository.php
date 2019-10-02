<?php
/**
 * @file
 */

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Statistics\Models\TeamParticipant;
use VSV\GVQ_API\Statistics\Repositories\Entities\ParticipantQuizEntity;

class ParticipantQuizDoctrineRepository extends AbstractDoctrineRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return ParticipantQuizEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function save(ParticipantQuizEntity $participantQuizEntity): void
    {
        $this->entityManager->persist($participantQuizEntity);
        $this->entityManager->flush();
    }
}
