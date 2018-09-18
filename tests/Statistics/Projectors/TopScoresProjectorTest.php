<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Models\DetailedTopScore;
use VSV\GVQ_API\Statistics\Repositories\DetailedTopScoreRepository;
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
     * @var DetailedTopScoreRepository|MockObject
     */
    private $detailedTopScoreRepository;

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

        /** @var DetailedTopScoreRepository|MockObject $detailedTopScoreRepository */
        $detailedTopScoreRepository = $this->createMock(DetailedTopScoreRepository::class);
        $this->detailedTopScoreRepository = $detailedTopScoreRepository;

        /** @var QuizRepository|MockObject $quizRepository */
        $quizRepository = $this->createMock(QuizRepository::class);
        $this->quizRepository = $quizRepository;

        $this->topScoresProjector = new TopScoresProjector(
            $this->topScoreRepository,
            $this->detailedTopScoreRepository,
            $this->quizRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_stores_a_top_score(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $email = new Email('par@ticipa.nt');

        $quizFinishedMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished($quiz->getId(), 12)
        );

        $this->quizRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->topScoreRepository
            ->expects($this->once())
            ->method('saveWhenHigher')
            ->with(
                new TopScore(
                    $email,
                    new NaturalNumber(12)
                )
            );

        $this->detailedTopScoreRepository
            ->expects($this->once())
            ->method('saveWhenHigher')
            ->with(
                new DetailedTopScore(
                    $email,
                    $quiz->getLanguage(),
                    $quiz->getChannel(),
                    new NaturalNumber(12)
                )
            );

        $this->topScoresProjector->handle($quizFinishedMessage);
    }
}
