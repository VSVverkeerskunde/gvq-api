<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
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
     * @var QuestionCounterRepository|MockObject
     */
    private $answeredCorrectRepository;

    /**
     * @var QuestionCounterRepository|MockObject
     */
    private $answeredInCorrectRepository;

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

        /** @var QuestionCounterRepository|MockObject $answeredCorrectRepository */
        $answeredCorrectRepository = $this->createMock(QuestionCounterRepository::class);
        $this->answeredCorrectRepository = $answeredCorrectRepository;

        /** @var QuestionCounterRepository|MockObject $answeredInCorrectRepository */
        $answeredInCorrectRepository = $this->createMock(QuestionCounterRepository::class);
        $this->answeredInCorrectRepository = $answeredInCorrectRepository;

        $this->questionDifficultyRedisRepository = new QuestionDifficultyRedisRepository(
            $this->redis,
            $this->answeredCorrectRepository,
            $this->answeredInCorrectRepository,
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

        $this->answeredCorrectRepository->expects($this->once())
            ->method('getCount')
            ->with($question)
            ->willReturn(new NaturalNumber(6));

        $this->answeredInCorrectRepository->expects($this->once())
            ->method('getCount')
            ->with($question)
            ->willReturn(new NaturalNumber(4));

        $this->redis->expects($this->exactly(2))
            ->method('zAdd')
            ->withConsecutive(
                [
                    'answered_correct_fr',
                    0.6,
                    $question->getId()->toString(),
                ],
                [
                    'answered_incorrect_fr',
                    0.4,
                    $question->getId()->toString()
                ]
            );

        $this->questionDifficultyRedisRepository->update($question);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_best_range_of_questions_for_language(): void
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
                    $accidentQuestion->getId()->toString() => 0.6,
                    $generalQuestion->getId()->toString() => 0.7,
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

        $questionDifficulties = $this->questionDifficultyRedisRepository->getBestRange(
            new Language(Language::FR),
            new NaturalNumber(1)
        );

        $this->assertEquals(
            new QuestionDifficulties(
                new QuestionDifficulty(
                    $accidentQuestion,
                    new NaturalNumber(60)
                ),
                new QuestionDifficulty(
                    $generalQuestion,
                    new NaturalNumber(70)
                )
            ),
            $questionDifficulties
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_worst_range_of_questions_for_language(): void
    {
        $accidentQuestion = ModelsFactory::createAccidentQuestion();
        $generalQuestion = ModelsFactory::createGeneralQuestion();

        $this->redis->expects($this->once())
            ->method('zRevRange')
            ->with(
                'answered_incorrect_fr',
                0,
                1,
                true
            )
            ->willReturn(
                [
                    $accidentQuestion->getId()->toString() => 0.11,
                    $generalQuestion->getId()->toString() => 0.22,
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

        $questionDifficulties = $this->questionDifficultyRedisRepository->getWorstRange(
            new Language(Language::FR),
            new NaturalNumber(1)
        );

        $this->assertEquals(
            new QuestionDifficulties(
                new QuestionDifficulty(
                    $accidentQuestion,
                    new NaturalNumber(11)
                ),
                new QuestionDifficulty(
                    $generalQuestion,
                    new NaturalNumber(22)
                )
            ),
            $questionDifficulties
        );
    }
}
