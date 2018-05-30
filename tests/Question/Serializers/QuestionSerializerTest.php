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
}
