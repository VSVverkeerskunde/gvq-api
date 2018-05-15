<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
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
        $categoryEntity = $this->entityManager->merge(
            $questionEntity->getCategoryEntity()
        );
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
        /** @var QuestionEntity|null $question */
        $questionEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $questionEntity ? $questionEntity->toQuestion() : null;
    }
}
