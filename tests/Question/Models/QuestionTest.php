<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use League\Uri\Uri;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\ValueObjects\Language;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuestionTest extends TestCase
{
    /**
     * @var Question
     */
    private $question;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->question = new Question(
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
            Uri::createFromString(
                'https://vragendatabank.s3-eu-west-1.amazonaws.com/styles/verkeersquiz_430x1/s3/01.07.jpg?itok=6-35lj-4'
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            ),
            ...[
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
                ),
            ]
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id()
    {
        $this->assertEquals(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            $this->question->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_language()
    {
        $this->assertEquals(
            new Language('fr'),
            $this->question->getLanguage()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_year()
    {
        $this->assertEquals(
            new Year(2018),
            $this->question->getYear()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_category()
    {
        $this->assertEquals(
            new Category(
                Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
                new NotEmptyString('EHBO/Ongeval/Verzekering')
            ),
            $this->question->getCategory()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_question_text()
    {
        $this->assertEquals(
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            $this->question->getQuestionText()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_picture_uri()
    {
        $this->assertEquals(
            Uri::createFromString(
                'https://vragendatabank.s3-eu-west-1.amazonaws.com/styles/verkeersquiz_430x1/s3/01.07.jpg?itok=6-35lj-4'
            ),
            $this->question->getPictureUri()
        );
    }

    /**
     * @test
     */
    public function it_stores_answers()
    {
        $this->assertEquals(
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
            $this->question->getAnswers()
        );
    }

    /**
     * @test
     */
    public function it_stores_feedback()
    {
        $this->assertEquals(
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            ),
            $this->question->getFeedback()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_be_archived()
    {
        $archivedOn = new \DateTimeImmutable('2018-10-12T12:02:53+00:00');

        $this->question->archiveOn($archivedOn);

        $this->assertEquals(
            new \DateTimeImmutable('2018-10-12T12:02:53+00:00'),
            $this->question->getArchivedOn()
        );
        $this->assertTrue(
            $this->question->isArchived()
        );
    }

    /**
     * @test
     */
    public function it_gets_created_as_not_archived()
    {
        $this->assertFalse(
            $this->question->isArchived()
        );
        $this->assertNull(
            $this->question->getArchivedOn()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_not_be_archived_twice()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(
            'The question with id: "448c6bd8-0075-4302-a4de-fe34d1554b8d" was already archived.'
        );

        $archivedOn = new \DateTimeImmutable('2018-10-12T12:02:53+00:00');

        $this->question->archiveOn($archivedOn);
        $this->question->archiveOn($archivedOn);
    }
}
