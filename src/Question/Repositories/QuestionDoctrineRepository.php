<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\Entities\AnswerEntity;
use VSV\GVQ_API\Question\Repositories\Entities\QuestionEntity;
use VSV\GVQ_API\Question\ValueObjects\Year;

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
        // Make sure the question exists,
        // otherwise merge will create a new question.
        $existingQuestionEntity = $this->getEntityById($question->getId());
        if ($existingQuestionEntity === null) {
            throw new EntityNotFoundException("Invalid question supplied");
        }

        // A question with 3 answers can be reduced to a question with 2 answers.
        // Make sure to delete this answer that was removed by the user.
        $newQuestionEntity = QuestionEntity::fromQuestion($question);
        $this->deleteRemovedAnswers($newQuestionEntity, $existingQuestionEntity);

        $this->entityManager->merge($newQuestionEntity);
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
    public function getSubset(
        Language $language,
        Category $category,
        Year $year,
        PositiveNumber $amount
    ): ?Questions {
        $questionEntities = $this->objectRepository->findBy(
            [
                'categoryEntity' => $category->getId()->toString(),
                'language' => $language->toNative(),
                'year' => $year->toNative(),
            ]
        );

        $pickedQuestionEntities = [];
        $amount = $amount->toNative();

        if (sizeof($questionEntities) >= $amount) {
            shuffle($questionEntities);
            for ($i = 0; $i < $amount; $i++) {
                $pickedQuestionEntities[] = $questionEntities[$i];
            }
        }

        if (empty($pickedQuestionEntities)) {
            return null;
        }

        return new Questions(
            ...array_map(
                function (QuestionEntity $questionEntity) {
                    return $questionEntity->toQuestion();
                },
                $pickedQuestionEntities
            )
        );
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

    /**
     * @param QuestionEntity $newQuestionEntity
     * @param QuestionEntity $existingQuestionEntity
     */
    private function deleteRemovedAnswers(
        QuestionEntity $newQuestionEntity,
        QuestionEntity $existingQuestionEntity
    ): void {
        $existingAnswers = $existingQuestionEntity->getAnswerEntities()->toArray();
        $newAnswers = $newQuestionEntity->getAnswerEntities()->toArray();

        $answersToDelete = array_udiff(
            $existingAnswers,
            $newAnswers,
            function (AnswerEntity $a1, AnswerEntity $a2) {
                return strcmp($a1->getId(), $a2->getId());
            }
        );

        foreach ($answersToDelete as $answerToDelete) {
            $this->entityManager->remove($answerToDelete);
            $this->entityManager->flush();
        }
    }
}
