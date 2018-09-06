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
    private $topScores;

    /**
     * @var QuizRepository|MockObject
     */
    private $quizzes;

    /**
     * @var TopScoresProjector
     */
    private $projector;

    public function setUp()
    {
        /** @var TopScoreRepository|MockObject $topScores */
        $topScores = $this->createMock(TopScoreRepository::class);
        /** @var QuizRepository|MockObject $quizzes */
        $quizzes = $this->createMock(QuizRepository::class);

        $this->topScores = $topScores;
        $this->quizzes = $quizzes;
        $this->projector = new TopScoresProjector($topScores, $quizzes);
    }

    /**
     * @test
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

        $this->quizzes
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->topScores
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn(null);

        $this->topScores
            ->expects($this->once())
            ->method('save')
            ->with(new TopScore($email, new NaturalNumber(16)));

        $this->projector->handle($quizFinishedMessage);
    }

    /**
     * @test
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

        $this->quizzes
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->topScores
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn(new TopScore($email, new NaturalNumber(17)));

        $this->topScores
            ->expects($this->never())
            ->method('save');

        $this->projector->handle($quizFinishedMessage);
    }

    /**
     * @test
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

        $this->quizzes
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->topScores
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn(new TopScore($email, new NaturalNumber(17)));

        $this->topScores
            ->expects($this->once())
            ->method('save')
            ->with(new TopScore($email, new NaturalNumber(19)));

        $this->projector->handle($quizFinishedMessage);
    }
}
