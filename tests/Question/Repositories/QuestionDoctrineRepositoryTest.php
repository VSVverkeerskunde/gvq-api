<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\Entities\QuestionEntity;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
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

        $updatedQuestion = new Question(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            new Language('fr'),
            new Year(2018),
            new Category(
                Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
                new NotEmptyString('EHBO/Ongeval/Verzekering')
            ),
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            new NotEmptyString(
                'b746b623-a86f-4384-9ebc-51af80eb6bcc.jpg'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new NotEmptyString('Non.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new NotEmptyString('Non.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new NotEmptyString('Non.'),
                    true
                )
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            )
        );
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
