<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Commands\StartQuiz;

class StartQuizSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new StartQuizDenormalizer(),
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
    public function it_can_deserialize_to_quiz_started(): void
    {
        $startQuizAsJson = ModelsFactory::createJson('start_quiz');
        $startQuiz = ModelsFactory::createStartQuiz();

        $actualStartQuiz = $this->serializer->deserialize(
            $startQuizAsJson,
            StartQuiz::class,
            'json'
        );

        $this->assertEquals(
            $startQuiz,
            $actualStartQuiz
        );
    }
}
