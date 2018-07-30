<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class QuizStartedListenerTest extends TestCase
{
    /**
     * @var QuizRepository|MockObject
     */
    private $quizRepository;

    /**
     * @var StartedQuizRepository|MockObject
     */
    private $startedQuizRepository;

    /**
     * @var QuizStartedListener
     */
    private $quizStartedListener;

    protected function setUp(): void
    {
        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        /** @var StartedQuizRepository|MockObject $startedQuizRepository */
        $startedQuizRepository = $this->createMock(StartedQuizRepository::class);
        $this->startedQuizRepository = $startedQuizRepository;

        $this->quizStartedListener = new QuizStartedListener(
            $this->quizRepository,
            $this->startedQuizRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_started()
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $domainMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizStarted(
                $quiz->getId(),
                $quiz
            )
        );

        $this->quizRepository->expects($this->once())
            ->method('save')
            ->with($quiz);

        $this->startedQuizRepository->expects($this->once())
            ->method('incrementTotal')
            ->with(StatisticsKey::createFromQuiz($quiz));

        $this->quizStartedListener->handle($domainMessage);
    }
}
