<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Contest\Repositories\Entities\ContestParticipationEntity;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\User\ValueObjects\Email;

class ContestParticipationDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var ContestParticipationDoctrineRepository
     */
    private $contestParticipationDoctrineRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contestParticipationDoctrineRepository = new ContestParticipationDoctrineRepository(
            $this->entityManager
        );
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return ContestParticipationEntity::class;
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_save_a_contest_participation(): void
    {
        $contestParticipation = ModelsFactory::createQuizContestParticipation();

        $this->contestParticipationDoctrineRepository->save($contestParticipation);

        $contestParticipations = $this->contestParticipationDoctrineRepository->getAll();

        $this->assertEquals(
            new ContestParticipations($contestParticipation),
            $contestParticipations
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_iterate_over_contest_participations(): void
    {
        $contestParticipations = [
            ModelsFactory::createQuizContestParticipation(),
            ModelsFactory::createCupContestParticipation(),
        ];

        foreach ($contestParticipations as $contestParticipation) {
            $this->contestParticipationDoctrineRepository->save($contestParticipation);
        }

        $traversable = $this->contestParticipationDoctrineRepository->getAllAsTraversable();
        $actualContestParticipations = [];
        foreach ($traversable as $contestParticipation) {
            $actualContestParticipations[] = $contestParticipation;
        }

        $this->assertEquals(
            $contestParticipations,
            $actualContestParticipations
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_returns_null_when_no_contest_participations_present(): void
    {
        $contestParticipations = $this->contestParticipationDoctrineRepository->getAll();

        $this->assertNull($contestParticipations);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_by_year_and_email(): void
    {
        $contestParticipation = ModelsFactory::createQuizContestParticipation();

        $this->contestParticipationDoctrineRepository->save($contestParticipation);

        $contestParticipations = $this->contestParticipationDoctrineRepository->getAllByYearAndEmail(
            new Year(2018),
            new Email('par@ticipa.nt')
        );

        $this->assertEquals(
            new ContestParticipations($contestParticipation),
            $contestParticipations
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_by_year_and_email_and_channel(): void
    {
        $expectedContestParticipation = ModelsFactory::createQuizContestParticipation();

        $this->contestParticipationDoctrineRepository->save($expectedContestParticipation);

        $foundContestParticipation = $this->contestParticipationDoctrineRepository->getByYearAndEmailAndChannel(
            new Year(2018),
            new Email('par@ticipa.nt'),
            new QuizChannel(QuizChannel::INDIVIDUAL)
        );

        $this->assertEquals(
            $expectedContestParticipation,
            $foundContestParticipation
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_returns_null_when_no_contest_for_year_and_email_and_channel(): void
    {
        $foundContestParticipation = $this->contestParticipationDoctrineRepository->getByYearAndEmailAndChannel(
            new Year(2018),
            new Email('jane@gvq.be'),
            new QuizChannel(QuizChannel::INDIVIDUAL)
        );

        $this->assertNull($foundContestParticipation);
    }
}
