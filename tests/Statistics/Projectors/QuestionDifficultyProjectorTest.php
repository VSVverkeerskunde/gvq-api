<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Statistics\Repositories\QuestionCounterRepository;
use VSV\GVQ_API\Statistics\Repositories\QuestionDifficultyRepository;

class QuestionDifficultyProjectorTest extends TestCase
{
    /**
     * @var QuestionDifficultyRepository|MockObject
     */
    private $questionDifficultyRepository;

    /**
     * @var QuestionCounterRepository|MockObject
     */
    private $questionAnsweredCorrectRepository;

    /**
     * @var QuestionCounterRepository|MockObject
     */
    private $questionAnsweredInCorrectRepository;

    /**
     * @var QuestionDifficultyProjector
     */
    private $questionDifficultyProjector;

    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @var Question
     */
    private $question;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        /** @var QuestionDifficultyRepository|MockObject $questionDifficultyRepository */
        $questionDifficultyRepository = $this->createMock(QuestionDifficultyRepository::class);
        $this->questionDifficultyRepository = $questionDifficultyRepository;

        /** @var QuestionCounterRepository|MockObject $questionAnsweredCorrectRepository */
        $questionAnsweredCorrectRepository = $this->createMock(QuestionCounterRepository::class);
        $this->questionAnsweredCorrectRepository = $questionAnsweredCorrectRepository;

        /** @var QuestionCounterRepository|MockObject $questionAnsweredInCorrectRepository */
        $questionAnsweredInCorrectRepository = $this->createMock(QuestionCounterRepository::class);
        $this->questionAnsweredInCorrectRepository = $questionAnsweredInCorrectRepository;

        $this->questionDifficultyProjector = new QuestionDifficultyProjector(
            $this->questionDifficultyRepository,
            $this->questionAnsweredCorrectRepository,
            $this->questionAnsweredInCorrectRepository
        );

        $this->quiz = ModelsFactory::createCompanyQuiz();
        $this->question = $this->quiz->getQuestions()->getIterator()->current();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_handle_answered_correct(): void
    {
        $answeredCorrectDomainMessage = ModelsFactory::createAnsweredCorrectDomainMessage(
            $this->quiz,
            $this->question
        );

        $this->questionAnsweredCorrectRepository->expects($this->once())
            ->method('increment')
            ->with($this->question);

        $this->questionAnsweredInCorrectRepository->expects($this->never())
            ->method('increment');

        $this->questionDifficultyRepository->expects($this->once())
            ->method('update')
            ->with($this->question);

        $this->questionDifficultyProjector->handle($answeredCorrectDomainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_handle_answered_incorrect(): void
    {
        $answeredInCorrectDomainMessage = ModelsFactory::createAnsweredInCorrectDomainMessage(
            $this->quiz,
            $this->question
        );

        $this->questionAnsweredCorrectRepository->expects($this->never())
            ->method('increment');

        $this->questionAnsweredInCorrectRepository->expects($this->once())
            ->method('increment')
            ->with($this->question);

        $this->questionDifficultyRepository->expects($this->once())
            ->method('update')
            ->with($this->question);

        $this->questionDifficultyProjector->handle($answeredInCorrectDomainMessage);
    }
}
