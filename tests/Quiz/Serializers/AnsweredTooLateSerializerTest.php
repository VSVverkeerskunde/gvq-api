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
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;

class AnsweredTooLateSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $answeredTooLateAsJson;

    /**
     * @var AnsweredTooLate
     */
    private $answeredTooLate;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $normalizers = [
            new AnsweredTooLateNormalizer(
                new QuestionNormalizer(
                    new CategoryNormalizer(),
                    new AnswerNormalizer()
                )
            ),
            new AnsweredTooLateDenormalizer(
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

        $this->answeredTooLateAsJson = ModelsFactory::createJson('answered_too_late');
        $this->answeredTooLate = ModelsFactory::createAnsweredTooLate();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->answeredTooLate,
            'json'
        );

        $this->assertEquals(
            $this->answeredTooLateAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_answered_incorrect(): void
    {
        $actualAnsweredTooLate = $this->serializer->deserialize(
            $this->answeredTooLateAsJson,
            AnsweredTooLate::class,
            'json'
        );

        $this->assertEquals(
            $this->answeredTooLate,
            $actualAnsweredTooLate
        );
    }
}
