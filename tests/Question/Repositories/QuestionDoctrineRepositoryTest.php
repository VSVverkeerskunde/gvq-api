<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use League\Uri\Uri;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\ValueObjects\Language;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;
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
     * @throws \Doctrine\DBAL\DBALException
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

        $category = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
        $categoryDoctrineRepository->save($category);

        $this->question = new Question(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            new Language('fr'),
            new Year(2018),
            $category,
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            Uri::createFromString(
                'https://vragendatabank.s3-eu-west-1.amazonaws.com/styles/verkeersquiz_430x1/s3/01.07.jpg?itok=6-35lj-4'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new NotEmptyString('Oui, mais uniquement en agglomération.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
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
    }

    /**
     * @test
     *
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function it_can_save_a_question(): void
    {
        $this->questionDoctrineRepository->save($this->question);

        $this->entityManager->clear();
        $foundQuestion = $this->questionDoctrineRepository->getById(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d')
        );

        $this->assertEquals(
            $this->question,
            $foundQuestion,
            '',
            0.0,
            10,
            true,
            false
        );
    }
}
