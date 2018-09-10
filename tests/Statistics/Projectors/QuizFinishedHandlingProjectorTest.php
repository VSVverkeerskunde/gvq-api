<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
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

    /**
     * @var int
     */
    protected $score;

    protected function setUp(): void
    {
        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        $this->quiz = $this->createQuiz();
        $this->score = 10;
    }

    /**
     * @return DomainMessage
     */
    protected function createDomainMessage(): DomainMessage
    {
        return DomainMessage::recordNow(
            $this->quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished(
                $this->quiz->getId(),
                $this->score
            )
        );
    }

    protected function doCommonQuizRepositoryExpect(): void
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
