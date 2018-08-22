<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StartedQuizesProjectorTest extends TestCase
{
    /**
     * @var StartedQuizRepository|MockObject
     */
    private $startedQuizRepository;

    /**
     * @var StartedQuizesProjector
     */
    private $startedQuizesProjector;

    protected function setUp(): void
    {
        /** @var StartedQuizRepository|MockObject $startedQuizRepository */
        $startedQuizRepository = $this->createMock(StartedQuizRepository::class);
        $this->startedQuizRepository = $startedQuizRepository;

        $this->startedQuizesProjector = new StartedQuizesProjector(
            $this->startedQuizRepository
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

        $this->startedQuizRepository->expects($this->once())
            ->method('incrementCount')
            ->with(StatisticsKey::createFromQuiz($quiz));

        $this->startedQuizesProjector->handle($domainMessage);
    }
}
