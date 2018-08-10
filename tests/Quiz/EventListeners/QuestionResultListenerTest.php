<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;

class QuestionResultListenerTest extends TestCase
{
    /**
     * @var QuestionResultRepository|MockObject
     */
    private $questionResultRepository;

    /**
     * @var QuestionResultListener
     */
    private $questionResultListener;

    protected function setUp(): void
    {
        /** @var QuestionResultRepository|MockObject $questionResultRepository */
        $questionResultRepository = $this->createMock(QuestionResultRepository::class);
        $this->questionResultRepository = $questionResultRepository;

        $this->questionResultListener = new QuestionResultListener(
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

        $this->questionResultListener->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_answered_incorrect(): void
    {
        $answeredIncorrect = ModelsFactory::createAnsweredIncorrect(false);

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

        $this->questionResultListener->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_answered_too_late(): void
    {
        $answeredIncorrect = ModelsFactory::createAnsweredIncorrect(true);

        $domainMessage = DomainMessage::recordNow(
            $answeredIncorrect->getId(),
            0,
            new Metadata(),
            $answeredIncorrect
        );

        $questionResult = ModelsFactory::createCustomQuestionResult(
            $answeredIncorrect->getQuestion(),
            true,
            null
        );

        $this->questionResultRepository->expects($this->once())
            ->method('save')
            ->with(
                $answeredIncorrect->getId(),
                $questionResult
            );

        $this->questionResultListener->handle($domainMessage);
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

        $this->questionResultListener->handle($domainMessage);
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
        $this->questionResultListener->handle($domainMessage);
    }
}
