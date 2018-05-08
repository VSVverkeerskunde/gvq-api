<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Question;

class QuestionDoctrineRepository extends AbstractDoctrineRepository implements QuestionRepository
{
    /**
     * @inheritdoc
     */
    public function getRepositoryName(): string
    {
        return Question::class;
    }

    /**
     * @param Question $question
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Question $question): void
    {
        $this->entityManager->persist($question);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $id
     * @return null|Question
     */
    public function getById(UuidInterface $id): ?Question
    {
        /** @var Question|null $question */
        $question = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $question;
    }
}
