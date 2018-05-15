<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use League\Uri\Uri;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\ValueObjects\Language;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuestionSerializerTest extends TestCase
{
    use ExpectedJsonTrait;

    /**
     * @var QuestionSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $questionAsJson;

    /**
     * @var Question
     */
    private $question;

    protected function setUp(): void
    {
        $this->serializer = new QuestionSerializer();

        $this->questionAsJson = $this->getExpectedJson('question.json');

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
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->question,
            'json'
        );

        $this->assertEquals(
            $this->questionAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_category(): void
    {
        $actualQuestion = $this->serializer->deserialize(
            $this->questionAsJson,
            Question::class,
            'json'
        );

        $this->assertEquals(
            $this->question,
            $actualQuestion
        );
    }
}
