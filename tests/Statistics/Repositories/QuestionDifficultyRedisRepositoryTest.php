<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulty;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class QuestionDifficultyRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var QuestionRepository|MockObject
     */
    private $questionRepository;

    /**
     * @var QuestionDifficultyRedisRepository
     */
    private $questionDifficultyRedisRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        /** @var QuestionRepository|MockObject $questionRepository */
        $questionRepository = $this->createMock(QuestionRepository::class);
        $this->questionRepository = $questionRepository;

        $this->questionDifficultyRedisRepository = new QuestionDifficultyRedisRepository(
            $this->redis,
            new NotEmptyString('answered_correct'),
            $this->questionRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_increment_count_for_one_question(): void
    {
        $question = ModelsFactory::createAccidentQuestion();

        $this->redis->expects($this->once())
            ->method('zIncrBy')
            ->with(
                'answered_correct_fr',
                1.0,
                $question->getId()->toString()
            );

        $this->questionDifficultyRedisRepository->increment($question);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_range_of_questions_for_language(): void
    {
        $accidentQuestion = ModelsFactory::createAccidentQuestion();
        $generalQuestion = ModelsFactory::createGeneralQuestion();

        $this->redis->expects($this->once())
            ->method('zRevRange')
            ->with(
                'answered_correct_fr',
                0,
                1,
                true
            )
            ->willReturn(
                [
                    $accidentQuestion->getId()->toString() => 3,
                    $generalQuestion->getId()->toString() => 2,
                ]
            );

        $this->questionRepository->expects($this->exactly(2))
            ->method('getById')
            ->withConsecutive(
                $accidentQuestion->getId(),
                $generalQuestion->getId()
            )
            ->willReturnOnConsecutiveCalls(
                $accidentQuestion,
                $generalQuestion
            );

        $questionDifficulties = $this->questionDifficultyRedisRepository->getRange(
            new Language(Language::FR),
            new NaturalNumber(1)
        );

        $this->assertEquals(
            new QuestionDifficulties(
                new QuestionDifficulty(
                    $accidentQuestion,
                    new NaturalNumber(3)
                ),
                new QuestionDifficulty(
                    $generalQuestion,
                    new NaturalNumber(2)
                )
            ),
            $questionDifficulties
        );
    }
}
