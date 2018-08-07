<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryDenormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;

class QuestionAskedSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new QuestionAskedNormalizer(
                new QuestionNormalizer(
                    new CategoryNormalizer(),
                    new AnswerNormalizer()
                )
            ),
            new QuestionAskedDenormalizer(
                new QuestionDenormalizer(
                    new CategoryDenormalizer(),
                    new AnswerDenormalizer()
                )
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_to_json(): void
    {
        $questionAskedAsJson = ModelsFactory::createJson('question_asked');
        $questionAsked = ModelsFactory::createQuestionAsked();

        $actualJson = $this->serializer->serialize(
            $questionAsked,
            'json'
        );

        $this->assertEquals(
            $questionAskedAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_question_asked(): void
    {
        $questionAskedAsJson = ModelsFactory::createJson('question_asked');
        $questionAsked = ModelsFactory::createQuestionAsked();

        $actualQuestionAsked = $this->serializer->deserialize(
            $questionAskedAsJson,
            QuestionAsked::class,
            'json'
        );

        $this->assertEquals(
            $questionAsked,
            $actualQuestionAsked
        );
    }
}
