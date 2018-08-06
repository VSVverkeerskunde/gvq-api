<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizFinished;

class QuizFinishedSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new QuizFinishedDenormalizer(),
            new QuizFinishedNormalizer(),
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
        $quizFinishedAsJson = ModelsFactory::createJson('quiz_finished');
        $quizFinished = ModelsFactory::createQuizFinished();

        $actualJson = $this->serializer->serialize(
            $quizFinished,
            'json'
        );

        $this->assertEquals(
            $quizFinishedAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_quiz_started(): void
    {
        $quizFinishedAsJson = ModelsFactory::createJson('quiz_finished');
        $quizFinished = ModelsFactory::createQuizFinished();

        $actualQuizStarted = $this->serializer->deserialize(
            $quizFinishedAsJson,
            QuizFinished::class,
            'json'
        );

        $this->assertEquals(
            $quizFinished,
            $actualQuizStarted
        );
    }
}
