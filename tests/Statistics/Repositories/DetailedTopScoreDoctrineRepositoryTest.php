<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\Models\DetailedTopScore;
use VSV\GVQ_API\Statistics\Repositories\Entities\DetailedTopScoreEntity;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class DetailedTopScoreDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var DetailedTopScoreDoctrineRepository
     */
    private $detailedTopScoreDoctrineRepository;

    /**
     * @var DetailedTopScore[]
     */
    private $detailedTopScores;

    protected function setUp(): void
    {
        parent::setUp();

        $this->detailedTopScoreDoctrineRepository = new DetailedTopScoreDoctrineRepository(
            $this->entityManager
        );

        $this->detailedTopScores = [
            new DetailedTopScore(
                new Email('jane@vsv.be'),
                new Language(Language::NL),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                new NaturalNumber(11)
            ),
            new DetailedTopScore(
                new Email('jane@vsv.be'),
                new Language(Language::NL),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                new NaturalNumber(11)
            ),
            new DetailedTopScore(
                new Email('jane@vsv.be'),
                new Language(Language::NL),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                new NaturalNumber(12)
            ),
            new DetailedTopScore(
                new Email('elli@vsv.be'),
                new Language(Language::NL),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                new NaturalNumber(10)
            ),
            new DetailedTopScore(
                new Email('john@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::CUP),
                new NaturalNumber(10)
            ),
            new DetailedTopScore(
                new Email('john@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                new NaturalNumber(10)
            ),
            new DetailedTopScore(
                new Email('john@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::COMPANY),
                new NaturalNumber(10)
            ),
            new DetailedTopScore(
                new Email('john@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::PARTNER),
                new NaturalNumber(10)
            ),
            new DetailedTopScore(
                new Email('andy@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::CUP),
                new NaturalNumber(12)
            ),
            new DetailedTopScore(
                new Email('andy@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                new NaturalNumber(12)
            ),
            new DetailedTopScore(
                new Email('andy@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::COMPANY),
                new NaturalNumber(12)
            ),
            new DetailedTopScore(
                new Email('andy@awsr.be'),
                new Language(Language::FR),
                new QuizChannel(QuizChannel::PARTNER),
                new NaturalNumber(12)
            )
        ];

        foreach ($this->detailedTopScores as $detailedTopScore) {
            $this->detailedTopScoreDoctrineRepository->saveWhenHigher($detailedTopScore);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return DetailedTopScoreEntity::class;
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_the_average_by_statistics_key(): void
    {
        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByKey(
                new StatisticsKey(StatisticsKey::INDIVIDUAL_NL)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByKey(
                new StatisticsKey(StatisticsKey::CUP_FR)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByKey(
                new StatisticsKey(StatisticsKey::INDIVIDUAL_FR)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByKey(
                new StatisticsKey(StatisticsKey::COMPANY_FR)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByKey(
                new StatisticsKey(StatisticsKey::PARTNER_FR)
            )
        );
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_the_average_by_channel(): void
    {
        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByChannel(
                new QuizChannel(QuizChannel::INDIVIDUAL)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByChannel(
                new QuizChannel(QuizChannel::COMPANY)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByChannel(
                new QuizChannel(QuizChannel::PARTNER)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByChannel(
                new QuizChannel(QuizChannel::CUP)
            )
        );
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_average_by_language(): void
    {
        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByLanguage(
                new Language(Language::NL)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getAverageByLanguage(
                new Language(Language::FR)
            )
        );
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_the_quiz_average(): void
    {
        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getQuizAverage(
                new Language(Language::NL)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getQuizAverage(
                new Language(Language::FR)
            )
        );

        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getQuizAverage(null)
        );
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_total_average(): void
    {
        $this->assertEquals(
            new Average(11),
            $this->detailedTopScoreDoctrineRepository->getTotalAverage()
        );
    }
}
