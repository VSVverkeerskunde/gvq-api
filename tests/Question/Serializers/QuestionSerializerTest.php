<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;

class QuestionSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
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

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $normalizers = [
            new QuestionNormalizer(
                new CategoryNormalizer(),
                new AnswerNormalizer()
            ),
            new QuestionDenormalizer(
                new CategoryDenormalizer(),
                new AnswerDenormalizer()
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->questionAsJson = ModelsFactory::createJson('question');
        $this->question = ModelsFactory::createAccidentQuestion();
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
    public function it_can_serialize_with_question_asked_context(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->question,
            'json',
            [
                'questionAsked' => true,
            ]
        );

        $jsonArray = json_decode($actualJson, true);

        $this->assertNull(
            $jsonArray['feedback']
        );

        foreach ($jsonArray['answers'] as $answer) {
            $this->assertNull(
                $answer['correct']
            );
        }
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_question(): void
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

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_an_archived_question_to_json(): void
    {
        $question = ModelsFactory::createArchivedAccidentQuestion();
        $questionAsJson = ModelsFactory::createJson('archived_question');

        $actualJson = $this->serializer->serialize(
            $question,
            'json'
        );

        $this->assertEquals(
            $questionAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_an_archived_question(): void
    {
        $question = ModelsFactory::createArchivedAccidentQuestion();
        $questionAsJson = ModelsFactory::createJson('archived_question');

        $actualQuestion = $this->serializer->deserialize(
            $questionAsJson,
            Question::class,
            'json'
        );

        $this->assertEquals(
            $question,
            $actualQuestion
        );
    }
}
