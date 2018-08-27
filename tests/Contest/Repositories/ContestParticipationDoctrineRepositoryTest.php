<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Contest\Repositories\Entities\ContestParticipationEntity;
use VSV\GVQ_API\Factory\ModelsFactory;

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
}
