<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Repositories\CurrentQuestionRepository;

class QuestionAskedListenerTest extends TestCase
{
    /**
     * @var CurrentQuestionRepository|MockObject
     */
    private $currentQuestionRepository;

    /**
     * @var QuestionAskedListener
     */
    private $questionAskedListener;

    protected function setUp(): void
    {
        /** @var CurrentQuestionRepository|MockObject $currentQuestionRepository */
        $currentQuestionRepository = $this->createMock(CurrentQuestionRepository::class);
        $this->currentQuestionRepository = $currentQuestionRepository;

        $this->questionAskedListener = new QuestionAskedListener(
            $this->currentQuestionRepository
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

        $this->currentQuestionRepository->expects($this->once())
            ->method('save')
            ->with(
                $questionAsked->getId(),
                $questionAsked->getQuestion()
            );

        $this->questionAskedListener->handle($domainMessage);
    }
}
