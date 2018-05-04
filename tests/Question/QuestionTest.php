<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new NotEmptyString('Oui, mais uniquement en agglomération.')
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.')
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new NotEmptyString('Non.')
                )
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            )
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
    public function it_stores_answers()
    {
        $this->assertEquals(
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new NotEmptyString('Oui, mais uniquement en agglomération.')
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.')
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new NotEmptyString('Non.')
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
}
