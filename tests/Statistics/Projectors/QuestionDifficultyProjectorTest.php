<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Statistics\Repositories\QuestionDifficultyRepository;

class QuestionDifficultyProjectorTest extends TestCase
{
    /**
     * @var QuestionDifficultyRepository|MockObject
     */
    private $questionCorrectRepository;

    /**
     * @var QuestionDifficultyRepository|MockObject
     */
    private $questionInCorrectRepository;

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
        /** @var QuestionDifficultyRepository|MockObject $questionCorrectRepository */
        $questionCorrectRepository = $this->createMock(QuestionDifficultyRepository::class);
        $this->questionCorrectRepository = $questionCorrectRepository;

        /** @var QuestionDifficultyRepository|MockObject $questionInCorrectRepository */
        $questionInCorrectRepository = $this->createMock(QuestionDifficultyRepository::class);
        $this->questionInCorrectRepository = $questionInCorrectRepository;

        $this->questionDifficultyProjector = new QuestionDifficultyProjector(
            $this->questionCorrectRepository,
            $this->questionInCorrectRepository
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
        $answeredCorrectDomainMessage = DomainMessage::recordNow(
            $this->quiz->getId(),
            0,
            new Metadata(),
            new AnsweredCorrect(
                $this->quiz->getId(),
                $this->question,
                $this->question->getAnswers()->getCorrectAnswer(),
                new \DateTimeImmutable()
            )
        );

        $this->questionCorrectRepository->expects($this->once())
            ->method('increment')
            ->with($this->question);

        $this->questionInCorrectRepository->expects($this->never())
            ->method('increment');

        $this->questionDifficultyProjector->handle($answeredCorrectDomainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_handle_answered_incorrect(): void
    {
        $answeredInCorrectDomainMessage = DomainMessage::recordNow(
            $this->quiz->getId(),
            0,
            new Metadata(),
            new AnsweredIncorrect(
                $this->quiz->getId(),
                $this->question,
                $this->question->getAnswers()->getCorrectAnswer(),
                new \DateTimeImmutable()
            )
        );

        $this->questionCorrectRepository->expects($this->never())
            ->method('increment');

        $this->questionInCorrectRepository->expects($this->once())
            ->method('increment')
            ->with($this->question);

        $this->questionDifficultyProjector->handle($answeredInCorrectDomainMessage);
    }
}
