<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;

abstract class QuizFinishedHandlingProjectorTest extends TestCase
{
    /**
     * @var QuizRepository|MockObject
     */
    protected $quizRepository;

    /**
     * @var Quiz
     */
    protected $quiz;

    protected function setUp(): void
    {
        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        $this->quiz = $this->createQuiz();
    }

    protected function mockQuizRepositoryGetById(): void
    {
        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($this->quiz->getId())
            ->willReturn($this->quiz);
    }

    /**
     * @return Quiz
     */
    abstract protected function createQuiz(): Quiz;
}
