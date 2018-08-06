<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;

class CurrentQuestionRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializer;

    /**
     * @var CurrentQuestionRedisRepository
     */
    private $currentQuestionRedisRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $this->serializer = $serializer;

        $this->currentQuestionRedisRepository = new CurrentQuestionRedisRepository(
            $this->redis,
            $this->serializer
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_save_the_current_question(): void
    {
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');
        $question = ModelsFactory::createGeneralQuestion();
        $questionAsJson = ModelsFactory::createJson('question');

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($question, 'json')
            ->willReturn($questionAsJson);

        $this->redis->expects($this->once())
            ->method('set')
            ->with('current_question_'.$quizId->toString(), $questionAsJson);

        $this->currentQuestionRedisRepository->save(
            $quizId,
            $question
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_the_current_question_by_quiz_id(): void
    {
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');
        $question = ModelsFactory::createGeneralQuestion();
        $questionAsJson = ModelsFactory::createJson('question');

        $this->redis->expects($this->once())
            ->method('get')
            ->with('current_question_'.$quizId->toString())
            ->willReturn($questionAsJson);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($questionAsJson, Question::class, 'json')
            ->willReturn($question);

        $currentQuestion = $this->currentQuestionRedisRepository->getById($quizId);

        $this->assertEquals($question, $currentQuestion);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_the_current_question_as_json_by_quiz_id(): void
    {
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');
        $questionAsJson = ModelsFactory::createJson('question');

        $this->redis->expects($this->once())
            ->method('get')
            ->with('current_question_'.$quizId->toString())
            ->willReturn($questionAsJson);

        $currentQuestionAsJson = $this->currentQuestionRedisRepository->getByIdAsJson($quizId);

        $this->assertEquals($questionAsJson, $currentQuestionAsJson);
    }
}
