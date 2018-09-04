<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Factory\QuestionsGenerator;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\Entities\QuestionEntity;
use VSV\GVQ_API\Question\ValueObjects\Year;

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
     * @var Category
     */
    private $category;

    /**
     * @var Questions
     */
    private $questions;

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
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
        $this->category = ModelsFactory::createGeneralCategory();
        $this->questions = QuestionsGenerator::generateForCategory($this->category);
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
     * @throws \Exception
     */
    public function it_throws_on_saving_a_question_with_non_existing_category(): void
    {
        $question = ModelsFactory::createQuestionWithMissingCategory();

        $this->expectException(ORMInvalidArgumentException::class);
        $this->expectExceptionMessage('A new entity was found through the relationship');

        $this->questionDoctrineRepository->save($question);
    }

    /**
     * @test
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function it_throws_on_updating_a_question_with_a_non_existing_category(): void
    {
        $this->questionDoctrineRepository->save($this->question);

        $updatedQuestion = ModelsFactory::createQuestionWithMissingCategory();

        $this->expectException(ORMInvalidArgumentException::class);
        $this->expectExceptionMessage('A new entity was found through the relationship');

        $this->questionDoctrineRepository->update($updatedQuestion);
    }

    /**
     * @test
     */
    public function it_can_delete_a_question(): void
    {
        $uuid = Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d');

        $this->questionDoctrineRepository->save($this->question);

        $this->questionDoctrineRepository->delete($uuid);

        $foundQuestion = $this->questionDoctrineRepository->getById($uuid);
        $this->assertNull($foundQuestion);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_an_archived_question(): void
    {
        $question = ModelsFactory::createArchivedAccidentQuestion();
        $this->questionDoctrineRepository->save($question);

        $foundQuestion = $this->questionDoctrineRepository->getById(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d')
        );

        $this->assertEquals(
            $question,
            $foundQuestion
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_unarchived_questions_by_year_and_language_and_category(): void
    {
        foreach ($this->questions as $question) {
            $this->questionDoctrineRepository->save($question);
        }

        $foundQuestions = $this->questionDoctrineRepository->getByYearAndLanguageAndCategoryAndNotArchived(
            new Year(2018),
            new Language('nl'),
            $this->category
        );

        $this->assertEquals(
            $this->questions,
            $foundQuestions
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_will_not_return_archived_questions_by_year_and_language_and_category(): void
    {
        foreach ($this->questions as $question) {
            /** @var Question $question */
            $question->archiveOn(new \DateTimeImmutable('2020-02-02T12:12:13+00:00'));
            $this->questionDoctrineRepository->save($question);
        }

        $foundQuestions = $this->questionDoctrineRepository->getByYearAndLanguageAndCategoryAndNotArchived(
            new Year(2018),
            new Language('nl'),
            $this->category
        );

        $this->assertNull(
            $foundQuestions
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_unarchived_questions_of_given_year_language_and_category_are_present(): void
    {
        $foundQuestions = $this->questionDoctrineRepository->getByYearAndLanguageAndCategoryAndNotArchived(
            new Year(2018),
            new Language('nl'),
            ModelsFactory::createGeneralCategory()
        );

        $this->assertNull(
            $foundQuestions
        );
    }

    /**
     * @test
     * @throws \Exception
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
