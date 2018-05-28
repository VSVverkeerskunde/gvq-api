<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\Entities\CategoryEntity;
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

        /** @var CategoryEntity $categoryEntity */
        $categoryEntity = $this->entityManager->find(
            CategoryEntity::class,
            $questionEntity->getCategoryEntity()->getId()
        );

        if ($categoryEntity == null) {
            throw new InvalidArgumentException(
                'Category with id: '.
                $questionEntity->getCategoryEntity()->getId().
                ' and name: '.
                $questionEntity->getCategoryEntity()->getName().
                ' not found.'
            );
        }

        $questionEntity->setCategoryEntity($categoryEntity);

        $this->entityManager->persist($questionEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function update(Question $question): void
    {
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
