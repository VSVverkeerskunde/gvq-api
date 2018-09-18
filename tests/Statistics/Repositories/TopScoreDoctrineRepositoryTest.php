<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Company\Repositories\CompanyDoctrineRepository;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\Models\TopScores;
use VSV\GVQ_API\Statistics\Repositories\Entities\TopScoreEntity;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\Repositories\UserDoctrineRepository;
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

        $employeeParticipationDoctrineRepository = new EmployeeParticipationDoctrineRepository(
            $this->entityManager
        );

        $employeeParticipations = ModelsFactory::createEmployeeParticipations();
        foreach ($employeeParticipations as $employeeParticipation) {
            $employeeParticipationDoctrineRepository->save($employeeParticipation);
        }

        $this->topScoreDoctrineRepository = new TopScoreDoctrineRepository(
            $this->entityManager
        );

        $this->topScores = [
            new TopScore(
                new Email('jane@vsv.be'),
                new NaturalNumber(11)
            ),
            new TopScore(
                new Email('elli@vsv.be'),
                new NaturalNumber(10)
            ),
            new TopScore(
                new Email('john@awsr.be'),
                new NaturalNumber(13)
            ),
            new TopScore(
                new Email('john@awsr.be'),
                new NaturalNumber(12)
            ),
            new TopScore(
                new Email('andy@awsr.be'),
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
    public function it_can_get_all_top_scores_by_company(): void
    {
        $topScores = $this->topScoreDoctrineRepository->getAllByCompany(
            Uuid::fromString('6e25425c-77cd-4899-9bfd-c2b8defb339f')
        );

        $this->assertEquals(
            new TopScores(
                new TopScore(
                    new Email('john@awsr.be'),
                    new NaturalNumber(13)
                ),
                new TopScore(
                    new Email('andy@awsr.be'),
                    new NaturalNumber(12)
                )
            ),
            $topScores
        );
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
    public function it_can_get_average_top_score(): void
    {
        $score = $this->topScoreDoctrineRepository->getAverage();

        $this->assertEquals(
            new Average(11.5),
            $score
        );
    }

    /**
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function it_can_get_average_top_score_for_company(): void
    {
        $score = $this->topScoreDoctrineRepository->getAverageForCompany(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106')
        );

        $this->assertEquals(
            new Average(10.5),
            $score
        );

        $score = $this->topScoreDoctrineRepository->getAverageForCompany(
            Uuid::fromString('6e25425c-77cd-4899-9bfd-c2b8defb339f')
        );

        $this->assertEquals(
            new Average(12.5),
            $score
        );
    }

    /**
     * @test
     */
    public function it_can_get_the_top_companies(): void
    {
        $userRepository = new UserDoctrineRepository(
            $this->entityManager
        );
        $userRepository->save(ModelsFactory::createUser());

        $companyRepository = new CompanyDoctrineRepository(
            $this->entityManager
        );
        $companyRepository->save(ModelsFactory::createCompany());
        $companyRepository->save(ModelsFactory::createAwsrCompany());

        $companies = $this->topScoreDoctrineRepository->getTopCompanies(
            new NaturalNumber(2)
        );

        $this->assertEquals(
            new Companies(
                ModelsFactory::createAwsrCompany()
            ),
            $companies
        );
    }
}
