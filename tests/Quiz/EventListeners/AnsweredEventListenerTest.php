<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;
use VSV\GVQ_API\Quiz\Repositories\CurrentQuestionRepository;

class AnsweredEventListenerTest extends TestCase
{
    /**
     * @var CurrentQuestionRepository|MockObject
     */
    private $currentQuestionRepository;

    /**
     * @var AnsweredEventListener
     */
    private $answeredEventListener;

    protected function setUp(): void
    {
        /** @var CurrentQuestionRepository|MockObject $currentQuestionRepository */
        $currentQuestionRepository = $this->createMock(CurrentQuestionRepository::class);
        $this->currentQuestionRepository = $currentQuestionRepository;

        $this->answeredEventListener = new AnsweredEventListener(
            $this->currentQuestionRepository
        );
    }

    /**
     * @test
     * @dataProvider answeredEventProvider
     * @param AbstractAnsweredEvent $answeredEvent
     * @throws \Exception
     */
    public function it_handles_answered_events(AbstractAnsweredEvent $answeredEvent): void
    {
        $domainMessage = DomainMessage::recordNow(
            $answeredEvent->getId(),
            0,
            new Metadata(),
            $answeredEvent
        );

        $this->currentQuestionRepository->expects($this->once())
            ->method('save')
            ->with(
                $answeredEvent->getId(),
                $answeredEvent->getQuestion()
            );

        $this->answeredEventListener->handle($domainMessage);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function answeredEventProvider(): array
    {
        return [
            [
                ModelsFactory::createAnsweredCorrect(),
            ],
            [
                ModelsFactory::createAnsweredIncorrect(),
            ],
        ];
    }
}
