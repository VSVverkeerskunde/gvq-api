<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Question\Models\Question;
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
     * @param Question $question
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
     * @param Question $question
     */
    public function update(Question $question): void
    {
        $this->entityManager->merge(
            QuestionEntity::fromQuestion($question)
        );
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $id
     * @return null|Question
     */
    public function getById(UuidInterface $id): ?Question
    {
        /** @var QuestionEntity|null $questionEntity */
        $questionEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $questionEntity ? $questionEntity->toQuestion() : null;
    }
}
