<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use League\Uri\Uri;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuestionsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_iterate_over_questions(): void
    {
        $question1 = new Question(
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

        $question2 = new Question(
            Uuid::fromString('5ffcac55-74e3-4836-a890-3e89a8a1cc15'),
            new Language('fr'),
            new Year(2018),
            new Category(
                Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
                new NotEmptyString('Algemene verkeersregels')
            ),
            new NotEmptyString(
                'Qui peut stationner devant ce garage?'
            ),
            Uri::createFromString(
                'https://vragendatabank.s3-eu-west-1.amazonaws.com/styles/verkeersquiz_430x1/s3/01.07.jpg?itok=6ablj-4'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('c4d5fa4d-b5bc-4d92-a201-a84abb0e3253'),
                    new NotEmptyString('Les habitants de cette maison.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('1ae8ea74-87f9-4e65-9458-d605888c3a54'),
                    new NotEmptyString('Personne.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('a33daadb-be3f-4625-b1ae-368611680bde'),
                    new NotEmptyString('Les habitants de cette maison et leurs visiteurs.'),
                    true
                )
            ),
            new NotEmptyString(
                'Il est interdit de stationner devant l’entrée des propriétés.'
            )
        );

        $expectedAnswers = [
            $question1,
            $question2,
        ];

        $questions = new Questions(...$expectedAnswers);

        $actualQuestions = [];
        foreach ($questions as $question) {
            $actualQuestions[] = $question;
        }

        $this->assertEquals($expectedAnswers, $actualQuestions);
    }
}
