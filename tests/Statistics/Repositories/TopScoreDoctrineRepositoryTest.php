<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Models\AverageScore;
use VSV\GVQ_API\Statistics\Repositories\Entities\TopScoreEntity;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class TopScoreDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var EmployeeParticipationDoctrineRepository
     */
    private $employees;

    /**
     * @var TopScoreDoctrineRepository
     */
    private $topScores;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employees = new EmployeeParticipationDoctrineRepository(
            $this->entityManager
        );

        $this->topScores = new TopScoreDoctrineRepository(
            $this->entityManager
        );
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_the_average_score_for_a_company(): void
    {
        $company = ModelsFactory::createCompany();
        $averageScore = $this->topScores->getAverageScoreForCompany($company->getId());
        $expectedAverageScore = new AverageScore($company->getId(), new NaturalNumber(0));

        $this->assertEquals($expectedAverageScore, $averageScore);
    }
}
