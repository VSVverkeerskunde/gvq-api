<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;

class QuestionResultProjectorTest extends TestCase
{
    /**
     * @var QuestionResultRepository|MockObject
     */
    private $questionResultRepository;

    /**
     * @var QuestionResultProjector
     */
    private $questionResultProjector;

    protected function setUp(): void
    {
        /** @var QuestionResultRepository|MockObject $questionResultRepository */
        $questionResultRepository = $this->createMock(QuestionResultRepository::class);
        $this->questionResultRepository = $questionResultRepository;

        $this->questionResultProjector = new QuestionResultProjector(
            $this->questionResultRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_question_asked(): void
    {
        $questionAsked = ModelsFactory::createQuestionAsked();

        $domainMessage = DomainMessage::recordNow(
            $questionAsked->getId(),
            0,
            new Metadata(),
            $questionAsked
        );

        $questionResult = ModelsFactory::createCustomQuestionResult(
            $questionAsked->getQuestion(),
            null,
            null
        );

        $this->questionResultRepository->expects($this->once())
            ->method('save')
            ->with(
                $questionAsked->getId(),
                $questionResult,
                [
                    'questionAsked' => true,
                ]
            );

        $this->questionResultProjector->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_answered_incorrect(): void
    {
        $answeredIncorrect = ModelsFactory::createAnsweredIncorrect();

        $domainMessage = DomainMessage::recordNow(
            $answeredIncorrect->getId(),
            0,
            new Metadata(),
            $answeredIncorrect
        );

        $questionResult = ModelsFactory::createCustomQuestionResult(
            $answeredIncorrect->getQuestion(),
            false,
            null
        );

        $this->questionResultRepository->expects($this->once())
            ->method('save')
            ->with(
                $answeredIncorrect->getId(),
                $questionResult
            );

        $this->questionResultProjector->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_answered_too_late(): void
    {
        $answeredTooLate = ModelsFactory::createAnsweredTooLate();

        $domainMessage = DomainMessage::recordNow(
            $answeredTooLate->getId(),
            0,
            new Metadata(),
            $answeredTooLate
        );

        $questionResult = ModelsFactory::createCustomQuestionResult(
            $answeredTooLate->getQuestion(),
            true,
            null
        );

        $this->questionResultRepository->expects($this->once())
            ->method('save')
            ->with(
                $answeredTooLate->getId(),
                $questionResult
            );

        $this->questionResultProjector->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_answered_correct(): void
    {
        $answeredCorrect = ModelsFactory::createAnsweredCorrect();

        $domainMessage = DomainMessage::recordNow(
            $answeredCorrect->getId(),
            0,
            new Metadata(),
            $answeredCorrect
        );

        $questionResult = ModelsFactory::createCustomQuestionResult(
            $answeredCorrect->getQuestion(),
            null,
            null
        );

        $this->questionResultRepository->expects($this->once())
            ->method('save')
            ->with(
                $answeredCorrect->getId(),
                $questionResult
            );

        $this->questionResultProjector->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $quizFinished = ModelsFactory::createQuizFinished();

        $domainMessage = DomainMessage::recordNow(
            $quizFinished->getId(),
            0,
            new Metadata(),
            $quizFinished
        );

        $questionResult = ModelsFactory::createCustomQuestionResult(
            ModelsFactory::createGeneralQuestion(),
            null,
            2
        );

        $this->questionResultRepository->expects($this->once())
            ->method('getById')
            ->with(
                $quizFinished->getId()
            )
        ->willReturn($questionResult);

        $this->questionResultRepository->expects($this->once())
            ->method('save')
            ->with(
                $quizFinished->getId(),
                $questionResult
            );
        $this->questionResultProjector->handle($domainMessage);
    }
}
