<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class FinishedQuizRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var FinishedQuizRepository
     */
    private $finishedQuizRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        $this->finishedQuizRepository = new FinishedQuizRedisRepository(
            $this->redis
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_total_count_of_started_quiz()
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $statisticsKey = StatisticsKey::createFromQuiz($quiz);

        $this->redis->expects($this->once())
            ->method('get')
            ->with('finished_quizzes_'.$statisticsKey->toNative());

        $this->finishedQuizRepository->getCount($statisticsKey);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_increment_count_of_finished_quiz()
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $statisticsKey = StatisticsKey::createFromQuiz($quiz);

        $this->redis->expects($this->once())
            ->method('incr')
            ->with('finished_quizzes_'.$statisticsKey->toNative());

        $this->finishedQuizRepository->incrementCount($statisticsKey);
    }
}
