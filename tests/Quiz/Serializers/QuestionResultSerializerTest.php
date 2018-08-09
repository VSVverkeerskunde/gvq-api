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
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionResultSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new QuestionResultNormalizer(
                new QuestionNormalizer(
                    new CategoryNormalizer(),
                    new AnswerNormalizer()
                )
            ),
            new QuestionResultDenormalizer(
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
        $questionResultAsJson = ModelsFactory::createJson('question_result');
        $questionResult = ModelsFactory::createQuestionResult();

        $actualJson = $this->serializer->serialize(
            $questionResult,
            'json'
        );

        $this->assertEquals(
            $questionResultAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_question_result(): void
    {
        $questionResultAsJson = ModelsFactory::createJson('question_result');
        $questionResult = ModelsFactory::createQuestionResult();

        $actualQuestionResult = $this->serializer->deserialize(
            $questionResultAsJson,
            QuestionResult::class,
            'json'
        );

        $this->assertEquals(
            $questionResult,
            $actualQuestionResult
        );
    }
}
