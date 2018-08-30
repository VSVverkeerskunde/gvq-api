<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class UniqueParticipantProjectorTest extends TestCase
{
    /**
     * @var UniqueParticipantRepository|MockObject
     */
    private $uniqueParticipantRepository;

    /**
     * @var QuizRepository|MockObject
     */
    private $quizRepository;

    /**
     * @var UniqueParticipantProjector
     */
    private $uniqueParticipantProjector;

    protected function setUp(): void
    {
        /** @var UniqueParticipantRepository|MockObject $uniqueParticipantRepository */
        $uniqueParticipantRepository = $this->createMock(UniqueParticipantRepository::class);
        $this->uniqueParticipantRepository = $uniqueParticipantRepository;

        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        $this->uniqueParticipantProjector = new UniqueParticipantProjector(
            $this->uniqueParticipantRepository,
            $this->quizRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();

        $domainMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished(
                $quiz->getId(),
                11
            )
        );

        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($quiz->getId())
            ->willReturn($quiz);

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('add')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant()
            );

        $this->uniqueParticipantProjector->handle($domainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_handles_quiz_finished_for_partner_channel(): void
    {
        $quiz = ModelsFactory::createPartnerQuiz();

        $domainMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished(
                $quiz->getId(),
                11
            )
        );

        $this->quizRepository->expects($this->once())
            ->method('getById')
            ->with($quiz->getId())
            ->willReturn($quiz);

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('add')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant()
            );

        $this->uniqueParticipantRepository->expects($this->once())
            ->method('addForPartner')
            ->with(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant(),
                $quiz->getPartner()
            );

        $this->uniqueParticipantProjector->handle($domainMessage);
    }
}
