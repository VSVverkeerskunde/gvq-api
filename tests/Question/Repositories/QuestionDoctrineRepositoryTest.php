<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\Entities\QuestionEntity;

class QuestionDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var QuestionDoctrineRepository
     */
    private $questionDoctrineRepository;

    /**
     * @var Question
     */
    private $question;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->questionDoctrineRepository = new QuestionDoctrineRepository(
            $this->entityManager
        );

        $categoryDoctrineRepository = new CategoryDoctrineRepository(
            $this->entityManager
        );
        $categoryDoctrineRepository->save(
            ModelsFactory::createAccidentCategory()
        );
        $categoryDoctrineRepository->save(
            ModelsFactory::createGeneralCategory()
        );

        $this->question = ModelsFactory::createAccidentQuestion();
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return QuestionEntity::class;
    }

    /**
     * @test
     */
    public function it_can_save_a_question(): void
    {
        $this->questionDoctrineRepository->save($this->question);

        $foundQuestion = $this->questionDoctrineRepository->getById(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d')
        );

        $this->assertEquals($this->question, $foundQuestion);
    }

    /**
     * @test
     */
    public function it_throws_on_saving_a_question_with_non_existing_category(): void
    {
        $question = ModelsFactory::createQuestionWithAlternateCategory();

        $this->expectException(ORMInvalidArgumentException::class);
        $this->expectExceptionMessage('A new entity was found through the relationship');

        $this->questionDoctrineRepository->save($question);
    }

    /**
     * @test
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function it_can_update_a_question(): void
    {
        $this->questionDoctrineRepository->save($this->question);

        $updatedQuestion = ModelsFactory::createUpdatedAccidentQuestion();
        $this->questionDoctrineRepository->update($updatedQuestion);

        $foundQuestion = $this->questionDoctrineRepository->getById(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d')
        );

        $this->assertEquals($updatedQuestion, $foundQuestion);
    }

    /**
     * @test
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function it_deletes_removed_answers(): void
    {
        $this->questionDoctrineRepository->save($this->question);

        $updatedQuestion = ModelsFactory::createUpdatedAccidentQuestionWithRemovedAnswer();
        $this->questionDoctrineRepository->update($updatedQuestion);

        $foundQuestion = $this->questionDoctrineRepository->getById(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d')
        );

        $this->assertEquals($updatedQuestion, $foundQuestion);
    }

    /**
     * @test
     * @throws EntityNotFoundException
     */
    public function it_throws_on_updating_a_non_existing_question(): void
    {
        $wrongQuestion = ModelsFactory::createAccidentQuestion();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Invalid question supplied');
        $this->questionDoctrineRepository->update($wrongQuestion);
    }

    /**
     * @test
     * @throws EntityNotFoundException
     */
    public function it_throws_on_updating_a_question_with_a_non_existing_category(): void
    {
        $this->questionDoctrineRepository->save($this->question);

        $updatedQuestion = ModelsFactory::createQuestionWithAlternateCategory();

        $this->expectException(ORMInvalidArgumentException::class);
        $this->expectExceptionMessage('A new entity was found through the relationship');

        $this->questionDoctrineRepository->update($updatedQuestion);
    }

    /**
     * @test
     */
    public function it_can_delete_a_question()
    {
        $uuid = Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d');

        $this->questionDoctrineRepository->save($this->question);

        $this->questionDoctrineRepository->delete($uuid);

        $foundQuestion = $this->questionDoctrineRepository->getById($uuid);
        $this->assertNull($foundQuestion);
    }

    /**
     * @test
     */
    public function it_can_get_all_questions(): void
    {
        $this->questionDoctrineRepository->save($this->question);

        $question2 = ModelsFactory::createGeneralQuestion();
        $this->questionDoctrineRepository->save($question2);

        $foundQuestions = $this->questionDoctrineRepository->getAll();

        $this->assertEquals(
            new Questions($this->question, $question2),
            $foundQuestions
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_questions_present(): void
    {
        $foundQuestions = $this->questionDoctrineRepository->getAll();

        $this->assertNull($foundQuestions);
    }
}
