<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class QuestionCounterRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var QuestionCounterRedisRepository
     */
    private $questionCounterRedisRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        $this->questionCounterRedisRepository = new QuestionCounterRedisRepository(
            $this->redis,
            new NotEmptyString('answered_correct')
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_increment_count_for_question(): void
    {
        $question = ModelsFactory::createGeneralQuestion();

        $this->redis->expects($this->once())
            ->method('incr')
            ->with('answered_correct_5ffcac55-74e3-4836-a890-3e89a8a1cc15');

        $this->questionCounterRedisRepository->increment($question);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_count_for_question(): void
    {
        $question = ModelsFactory::createGeneralQuestion();

        $this->redis->expects($this->once())
            ->method('get')
            ->with('answered_correct_5ffcac55-74e3-4836-a890-3e89a8a1cc15')
            ->willReturn(2);

        $this->assertEquals(
            new NaturalNumber(2),
            $this->questionCounterRedisRepository->getCount($question)
        );
    }
}
