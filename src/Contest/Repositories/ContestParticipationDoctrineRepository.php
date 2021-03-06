<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use Doctrine\ORM\Query\Expr\Join;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Contest\Repositories\Entities\ContestParticipationEntity;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\Repositories\Entities\TeamParticipantEntity;
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
     * @return \Traversable
     */
    public function getAllAsTraversable(): \Traversable
    {
        $batchSize = 10;
        $firstResult = 0;

        do {
            $queryBuilder = $this->entityManager->createQueryBuilder();

            $query = $queryBuilder->select('e')
                ->from(
                    'VSV\GVQ_API\Contest\Repositories\Entities\ContestParticipationEntity',
                    'e'
                )
                ->orderBy('e.id', 'ASC')
                ->setMaxResults($batchSize)
                ->setFirstResult($firstResult)
                ->getQuery();

            $currentBatchItemCount = 0;

            foreach ($query->iterate() as $contestParticipationEntities) {
                $currentBatchItemCount++;

                /** @var ContestParticipationEntity $entity */
                $entity = $contestParticipationEntities[0];
                $this->entityManager->detach($entity);

                yield $entity->toContestParticipation();
            }

            $firstResult += $batchSize;
        } while ($currentBatchItemCount == $batchSize);
    }

    public function getParticipantsInTeam(UuidInterface $teamId): \Traversable
    {
        $batchSize = 10;
        $firstResult = 0;

        do {
            $queryBuilder = $this->entityManager->createQueryBuilder();

            $query = $queryBuilder->select('e')
                ->from(
                    ContestParticipationEntity::class,
                    'e'
                )
                ->where($queryBuilder->expr()->eq('e.channel', ':channel'))
                ->innerJoin(TeamParticipantEntity::class, 't', Join::WITH, 'e.contestParticipant.email = t.email AND t.teamId=:team_id')
                ->orderBy('e.id', 'ASC')
                ->setMaxResults($batchSize)
                ->setFirstResult($firstResult)
                ->setParameter('team_id', $teamId->toString())
                ->setParameter('channel', 'cup')
                ->getQuery();

            $currentBatchItemCount = 0;

            foreach ($query->iterate() as $contestParticipationEntities) {
                $currentBatchItemCount++;

                /** @var ContestParticipationEntity $entity */
                $entity = $contestParticipationEntities[0];
                $this->entityManager->detach($entity);

                yield $entity->toContestParticipation();
            }

            $firstResult += $batchSize;
        } while ($currentBatchItemCount == $batchSize);
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
