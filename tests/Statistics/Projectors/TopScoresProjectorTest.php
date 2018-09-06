<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScoresProjectorTest extends TestCase
{
    /**
     * @var TopScoreRepository|MockObject
     */
    private $topScoreRepository;

    /**
     * @var QuizRepository|MockObject
     */
    private $quizRepository;

    /**
     * @var TopScoresProjector
     */
    private $topScoresProjector;

    public function setUp()
    {
        /** @var TopScoreRepository|MockObject $topScoreRepository */
        $topScoreRepository = $this->createMock(TopScoreRepository::class);
        $this->topScoreRepository = $topScoreRepository;

        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        $this->topScoresProjector = new TopScoresProjector(
            $this->topScoreRepository,
            $this->quizRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_should_save_the_first_score_as_top_score(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $email = new Email('par@ticipa.nt');

        $quizFinishedMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished($quiz->getId(), 16)
        );

        $this->quizRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->topScoreRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn(null);

        $this->topScoreRepository
            ->expects($this->once())
            ->method('save')
            ->with(new TopScore($email, new NaturalNumber(16)));

        $this->topScoresProjector->handle($quizFinishedMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_should_not_lower_an_existing_top_score(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $email = new Email('par@ticipa.nt');

        $quizFinishedMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished($quiz->getId(), 10)
        );

        $this->quizRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->topScoreRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn(new TopScore($email, new NaturalNumber(17)));

        $this->topScoreRepository
            ->expects($this->never())
            ->method('save');

        $this->topScoresProjector->handle($quizFinishedMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_should_increase_an_existing_top_score(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $email = new Email('par@ticipa.nt');

        $quizFinishedMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished($quiz->getId(), 19)
        );

        $this->quizRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->topScoreRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn(new TopScore($email, new NaturalNumber(17)));

        $this->topScoreRepository
            ->expects($this->once())
            ->method('save')
            ->with(new TopScore($email, new NaturalNumber(19)));

        $this->topScoresProjector->handle($quizFinishedMessage);
    }
}
