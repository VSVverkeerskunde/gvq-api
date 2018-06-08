<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuestionTest extends TestCase
{
    /**
     * @var Question
     */
    private $question;

    protected function setUp(): void
    {
        $this->question = ModelsFactory::createAccidentQuestion();
    }

    /**
     * @test
     */
    public function it_stores_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            $this->question->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_language(): void
    {
        $this->assertEquals(
            new Language('fr'),
            $this->question->getLanguage()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_year(): void
    {
        $this->assertEquals(
            new Year(2018),
            $this->question->getYear()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_category(): void
    {
        $this->assertEquals(
            ModelsFactory::createAccidentCategory(),
            $this->question->getCategory()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_question_text(): void
    {
        $this->assertEquals(
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            $this->question->getText()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_image_file_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('b746b623-a86f-4384-9ebc-51af80eb6bcc.jpg'),
            $this->question->getImageFileName()
        );
    }

    /**
     * @test
     */
    public function it_stores_answers(): void
    {
        $this->assertEquals(
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new PositiveNumber(1),
                    new NotEmptyString('Oui, mais uniquement en agglomération.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new PositiveNumber(2),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new PositiveNumber(3),
                    new NotEmptyString('Non.'),
                    true
                )
            ),
            $this->question->getAnswers()
        );
    }

    /**
     * @test
     * @dataProvider answersProvider
     * @param Answers $answers
     */
    public function it_throws_on_wrong_number_of_answers(Answers $answers): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount of answers must be 2 or 3.');

        new Question(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            new Language('fr'),
            new Year(2018),
            ModelsFactory::createAccidentCategory(),
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            new NotEmptyString(
                'b746b623-a86f-4384-9ebc-51af80eb6bcc.jpg'
            ),
            $answers,
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            )
        );
    }

    /**
     * @return Answers[][]
     */
    public function answersProvider(): array
    {
        $answer1 = new Answer(
            Uuid::fromString('b1a4a8a4-6419-449f-bde2-10122d90a916'),
            new PositiveNumber(1),
            new NotEmptyString('text'),
            false
        );
        $answer2 = new Answer(
            Uuid::fromString('bfc153e0-8fea-489b-9010-1dfe9f9dbba8'),
            new PositiveNumber(2),
            new NotEmptyString('text'),
            false
        );
        $answer3 = new Answer(
            Uuid::fromString('822dd8f9-c86b-4531-be92-b35627a21ba4'),
            new PositiveNumber(3),
            new NotEmptyString('text'),
            false
        );
        $answer4 = new Answer(
            Uuid::fromString('50f0551b-a239-4554-96dc-4f4778e8d63a'),
            new PositiveNumber(4),
            new NotEmptyString('text'),
            true
        );

        return [
            [
                new Answers(
                    $answer1,
                    $answer2,
                    $answer3,
                    $answer4
                ),
            ],
            [
                new Answers(
                    $answer1
                ),
            ],
        ];
    }

    /**
     * @test
     */
    public function it_stores_feedback(): void
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
    public function it_can_be_archived(): void
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
    public function it_gets_created_as_not_archived(): void
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
    public function it_can_not_be_archived_twice(): void
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
