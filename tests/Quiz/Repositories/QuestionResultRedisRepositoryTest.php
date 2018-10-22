<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionResultRedisRepositoryTest extends TestCase
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
     * @var QuestionResultRedisRepository
     */
    private $questionResultRedisRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $this->serializer = $serializer;

        $this->questionResultRedisRepository = new QuestionResultRedisRepository(
            $this->redis,
            $this->serializer
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_save_the_question_result(): void
    {
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');
        $questionResult = ModelsFactory::createQuestionResult();
        $questionResultAsJson = ModelsFactory::createJson('question_result');

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($questionResult, 'json')
            ->willReturn($questionResultAsJson);

        $this->redis->expects($this->once())
            ->method('setex')
            ->with(
                'question_result_'.$quizId->toString(),
                3600 * 10,
                $questionResultAsJson
            );

        $this->questionResultRedisRepository->save(
            $quizId,
            $questionResult
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_the_current_question_by_quiz_id(): void
    {
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');
        $questionResult = ModelsFactory::createQuestionResult();
        $questionResultAsJson = ModelsFactory::createJson('question_result');

        $this->redis->expects($this->once())
            ->method('get')
            ->with('question_result_'.$quizId->toString())
            ->willReturn($questionResultAsJson);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($questionResultAsJson, QuestionResult::class, 'json')
            ->willReturn($questionResult);

        $currentQuestion = $this->questionResultRedisRepository->getById($quizId);

        $this->assertEquals($questionResult, $currentQuestion);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_the_current_question_as_json_by_quiz_id(): void
    {
        $quizId = Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b');
        $questionResultAsJson = ModelsFactory::createJson('question_result');

        $this->redis->expects($this->once())
            ->method('get')
            ->with('question_result_'.$quizId->toString())
            ->willReturn($questionResultAsJson);

        $currentQuestionAsJson = $this->questionResultRedisRepository->getByIdAsJson($quizId);

        $this->assertEquals($questionResultAsJson, $currentQuestionAsJson);
    }
}
