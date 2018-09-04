<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Contest\Repositories\Entities\ContestParticipationEntity;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\User\ValueObjects\Email;

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

        $this->entityManager->persist($contestParticipantEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getByYearAndEmailAndChannel(
        Year $year,
        Email $email,
        QuizChannel $channel
    ): ?ContestParticipation {
        /** @var ContestParticipationEntity|null $contestParticipationEntity */
        $contestParticipationEntity = $this->objectRepository->findOneBy(
            [
                'year' => $year->toNative(),
                'contestParticipant.email' => $email->toNative(),
                'channel' => $channel->toNative(),
            ]
        );

        return $contestParticipationEntity !== null ?
            $contestParticipationEntity->toContestParticipation() : null;
    }

    /**
     * @inheritdoc
     */
    public function getAll(): ?ContestParticipations
    {
        /** @var ContestParticipationEntity[] $contestParticipationEntities */
        $contestParticipationEntities = $this->objectRepository->findAll();

        return $this->toContestParticipations($contestParticipationEntities);
    }

    /**
     * @param Year $year
     * @param Email $email
     * @return null|ContestParticipations
     */
    public function getAllByYearAndEmail(
        Year $year,
        Email $email
    ): ?ContestParticipations {
        /** @var ContestParticipationEntity[] $contestParticipationEntities */
        $contestParticipationEntities = $this->objectRepository->findBy(
            [
                'year' => $year->toNative(),
                'contestParticipant.email' => $email->toNative(),
            ]
        );

        return $this->toContestParticipations($contestParticipationEntities);
    }

    /**
     * @param ContestParticipationEntity[] $contestParticipationEntities
     * @return null|ContestParticipations
     */
    private function toContestParticipations(array $contestParticipationEntities): ?ContestParticipations
    {
        if (empty($contestParticipationEntities)) {
            return null;
        }

        return new ContestParticipations(
            ...array_map(
                function (ContestParticipationEntity $contestParticipationEntity) {
                    return $contestParticipationEntity->toContestParticipation();
                },
                $contestParticipationEntities
            )
        );
    }
}
