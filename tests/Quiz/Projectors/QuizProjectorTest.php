<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;

class QuizProjectorTest extends TestCase
{
    /**
     * @var QuizRepository|MockObject
     */
    private $quizRepository;

    /**
     * @var QuizProjector
     */
    private $quizProjector;

    protected function setUp(): void
    {
        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        $this->quizProjector = new QuizProjector(
            $this->quizRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_started(): void
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

        $this->quizProjector->handle($domainMessage);
    }
}
