<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class FinishedQuizzesProjectorTest extends TestCase
{
    /**
     * @var FinishedQuizRepository|MockObject
     */
    private $finishedQuizRepository;

    /**
     * @var QuizRepository|MockObject
     */
    private $quizRepository;

    /**
     * @var FinishedQuizzesProjector
     */
    private $finishedQuizzesProjector;

    protected function setUp(): void
    {
        /** @var FinishedQuizRepository|MockObject $finishedQuizRepository */
        $finishedQuizRepository = $this->createMock(FinishedQuizRepository::class);
        $this->finishedQuizRepository = $finishedQuizRepository;

        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        $this->finishedQuizzesProjector = new FinishedQuizzesProjector(
            $this->finishedQuizRepository,
            $this->quizRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $domainMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished(
                $quiz->getId(),
                10
            )
        );

        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($quiz->getId())
            ->willReturn($quiz);

        $this->finishedQuizRepository->expects($this->once())
            ->method('incrementCount')
            ->with(StatisticsKey::createFromQuiz($quiz));

        $this->finishedQuizzesProjector->handle($domainMessage);
    }
}
