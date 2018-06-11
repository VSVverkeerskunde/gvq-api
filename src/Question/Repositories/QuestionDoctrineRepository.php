<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\Entities\QuestionEntity;

class QuestionDoctrineRepository extends AbstractDoctrineRepository implements QuestionRepository
{
    /**
     * @inheritdoc
     */
    public function getRepositoryName(): string
    {
        return QuestionEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function save(Question $question): void
    {
        $questionEntity = QuestionEntity::fromQuestion($question);

        // The category object inside question is not managed,
        // therefore we need to use merge instead of persist.
        // When category wouldn't exist yet, the category is not created.
        $this->entityManager->merge($questionEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     * @throws EntityNotFoundException
     */
    public function update(Question $question): void
    {
        $questionEntity = $this->entityManager->find(
            QuestionEntity::class,
            $question->getId()
        );
        if ($questionEntity == null) {
            throw new EntityNotFoundException("Invalid question supplied");
        }

        $this->entityManager->merge(
            QuestionEntity::fromQuestion($question)
        );
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(UuidInterface $id): void
    {
        $questionEntity = $this->getEntityById($id);

        if ($questionEntity !== null) {
            $this->entityManager->merge($questionEntity);
            $this->entityManager->remove($questionEntity);
            $this->entityManager->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function getById(UuidInterface $id): ?Question
    {
        $questionEntity = $this->getEntityById($id);

        return $questionEntity ? $questionEntity->toQuestion() : null;
    }

    /**
     * @inheritdoc
     */
    public function getAll(): ?Questions
    {
        /** @var QuestionEntity[] $questionEntities */
        $questionEntities = $this->objectRepository->findAll();

        if (empty($questionEntities)) {
            return null;
        }

        return new Questions(
            ...array_map(
                function (QuestionEntity $questionEntity) {
                    return $questionEntity->toQuestion();
                },
                $questionEntities
            )
        );
    }

    /**
     * @param UuidInterface $id
     * @return QuestionEntity
     */
    private function getEntityById(UuidInterface $id): ?QuestionEntity
    {
        /** @var QuestionEntity|null $questionEntity */
        $questionEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $questionEntity;
    }
}
