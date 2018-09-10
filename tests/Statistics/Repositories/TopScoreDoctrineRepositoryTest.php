<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\Repositories\Entities\TopScoreEntity;
use VSV\GVQ_API\Statistics\ValueObjects\AverageScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScoreDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var TopScoreDoctrineRepository
     */
    private $topScoreDoctrineRepository;

    /**
     * @var TopScore[]
     */
    private $topScores;

    protected function setUp(): void
    {
        parent::setUp();

        $this->topScoreDoctrineRepository = new TopScoreDoctrineRepository(
            $this->entityManager
        );

        $this->topScores = [
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('john@awsr.be'),
                new NaturalNumber(13)
            ),
            new TopScore(
                new Email('john@awsr.be'),
                new NaturalNumber(12)
            ),
        ];

        foreach ($this->topScores as $topScore) {
            $this->topScoreDoctrineRepository->saveWhenHigher($topScore);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return TopScoreEntity::class;
    }

    /**
     * @test
     */
    public function it_can_get_a_top_score_by_email(): void
    {
        $foundTopScore = $this->topScoreDoctrineRepository->getByEmail(new Email('jane@vsv.be'));

        $this->assertEquals(
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(11)
            ),
            $foundTopScore
        );
    }

    /**
     * @test
     */
    public function it_only_stores_highest_top_score_for_a_user(): void
    {
        $foundTopScore = $this->topScoreDoctrineRepository->getByEmail(new Email('john@awsr.be'));

        $this->assertEquals(
            new TopScore(
                new Email('john@awsr.be'),
                new NaturalNumber(13)
            ),
            $foundTopScore
        );
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_average_top_score_for_company(): void
    {
        $employeeParticipationDoctrineRepository = new EmployeeParticipationDoctrineRepository(
            $this->entityManager
        );

        $employeeParticipations = ModelsFactory::createEmployeeParticipations();
        foreach ($employeeParticipations as $employeeParticipation) {
            $employeeParticipationDoctrineRepository->save($employeeParticipation);
        }

        $score = $this->topScoreDoctrineRepository->getAverageScoreForCompany(
            Uuid::fromString('da5f2e1f-43c9-4ffc-90c1-761c2bc2453e')
        );

        $this->assertEquals(
            new AverageScore(
                Uuid::fromString('da5f2e1f-43c9-4ffc-90c1-761c2bc2453e'),
                new NaturalNumber(11)
            ),
            $score
        );
    }
}
