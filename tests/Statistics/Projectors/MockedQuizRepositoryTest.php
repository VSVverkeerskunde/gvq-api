<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;

abstract class MockedQuizRepositoryTest extends TestCase
{
    /**
     * @var QuizRepository|MockObject
     */
    protected $quizRepository;

    protected function setUp(): void
    {
        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;
    }

    /**
     * @param Quiz $quiz
     */
    protected function mockQuizRepositoryGetById(Quiz $quiz): void
    {
        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($quiz->getId())
            ->willReturn($quiz);
    }
}
