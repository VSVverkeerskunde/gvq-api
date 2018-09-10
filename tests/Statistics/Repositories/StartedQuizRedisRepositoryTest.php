<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StartedQuizRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var StartedQuizRepository
     */
    private $startedQuizRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        $this->startedQuizRepository = new StartedQuizRedisRepository(
            $this->redis
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_total_count_of_started_quiz(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $statisticsKey = StatisticsKey::createFromQuiz($quiz);

        $this->redis->expects($this->once())
            ->method('get')
            ->with('started_quizzes_'.$statisticsKey->toNative());

        $this->startedQuizRepository->getCount($statisticsKey);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_increment_count_of_started_quiz(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $statisticsKey = StatisticsKey::createFromQuiz($quiz);

        $this->redis->expects($this->once())
            ->method('incr')
            ->with('started_quizzes_'.$statisticsKey->toNative());

        $this->startedQuizRepository->incrementCount($statisticsKey);
    }
}
