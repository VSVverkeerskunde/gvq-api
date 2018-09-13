<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Serializer\Tests\Model;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRepository;

class FinishedQuizzesProjectorTest extends QuizFinishedHandlingProjectorTest
{
    /**
     * @var FinishedQuizRepository|MockObject
     */
    private $finishedQuizRepository;

    /**
     * @var FinishedQuizzesProjector
     */
    private $finishedQuizzesProjector;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var FinishedQuizRepository|MockObject $finishedQuizRepository */
        $finishedQuizRepository = $this->createMock(FinishedQuizRepository::class);
        $this->finishedQuizRepository = $finishedQuizRepository;

        $this->finishedQuizzesProjector = new FinishedQuizzesProjector(
            $this->finishedQuizRepository,
            $this->quizRepository
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function createQuiz(): Quiz
    {
        return ModelsFactory::createIndividualQuiz();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $this->mockQuizRepositoryGetById();

        $this->finishedQuizRepository->expects($this->once())
            ->method('incrementCount')
            ->with(StatisticsKey::createFromQuiz($this->quiz));

        $this->finishedQuizzesProjector->handle(
            ModelsFactory::createQuizFinishedDomainMessage($this->quiz)
        );
    }
}
