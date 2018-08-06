<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\Entities\AnswerEntity;

class AnswerDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
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
     * @var AnswerDoctrineRepository
     */
    private $answerDoctrineRepository;

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $categoryDoctrineRepository = new CategoryDoctrineRepository(
            $this->entityManager
        );
        $categoryDoctrineRepository->save(
            ModelsFactory::createAccidentCategory()
        );

        $this->questionDoctrineRepository = new QuestionDoctrineRepository(
            $this->entityManager
        );
        $this->question = ModelsFactory::createAccidentQuestion();
        $this->questionDoctrineRepository->save($this->question);

        $this->answerDoctrineRepository = new AnswerDoctrineRepository(
            $this->entityManager
        );
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return AnswerEntity::class;
    }

    /**
     * @test
     */
    public function it_can_get_an_answer_by_id(): void
    {
        $answer = $this->answerDoctrineRepository->getById(
            Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab')
        );

        $this->assertEquals(
             new Answer(
                 Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                 new PositiveNumber(2),
                 new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                 false
             ),
            $answer
        );
    }
}
