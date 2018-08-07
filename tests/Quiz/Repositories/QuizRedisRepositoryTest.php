<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Models\Quiz;

class QuizRedisRepositoryTest extends TestCase
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
     * @var QuizRedisRepository
     */
    private $quizRedisRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $this->serializer = $serializer;

        $this->quizRedisRepository = new QuizRedisRepository(
            $this->redis,
            $this->serializer
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_save_a_quiz(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $quizAsJson = ModelsFactory::createJson('quiz_individual');

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($quiz, 'json')
            ->willReturn($quizAsJson);

        $this->redis->expects($this->once())
            ->method('set')
            ->with('quiz_'.$quiz->getId()->toString(), $quizAsJson);

        $this->quizRedisRepository->save($quiz);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_a_quiz_by_id(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $quizAsJson = ModelsFactory::createJson('quiz_individual');

        $this->redis->expects($this->once())
            ->method('get')
            ->with('quiz_'.$quiz->getId()->toString())
            ->willReturn($quizAsJson);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($quizAsJson, Quiz::class, 'json')
            ->willReturn($quiz);

        $actualQuiz = $this->quizRedisRepository->getById($quiz->getId());

        $this->assertEquals($quiz, $actualQuiz);
    }
}
