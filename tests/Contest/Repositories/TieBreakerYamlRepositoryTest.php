<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Contest\Models\TieBreakers;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;

class TieBreakerYamlRepositoryTest extends TestCase
{
    /**
     * @var TieBreakerYamlRepository
     */
    private $tieBreakersYamlRepository;

    protected function setUp()
    {
        $this->tieBreakersYamlRepository = new TieBreakerYamlRepository(
            __DIR__.'/../../Factory/Samples/tie_breakers.yaml'
        );
    }

    /**
     * @test
     */
    public function it_can_get_all_tie_breakers_for_a_given_year()
    {
        $tieBreakers = $this->tieBreakersYamlRepository->getAllByYear(new Year(2018));

        $this->assertEquals(
            new TieBreakers(
                ModelsFactory::createQuizTieBreaker(),
                ModelsFactory::createCupTieBreaker()
            ),
            $tieBreakers
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_year_is_not_present(): void
    {
        $this->assertNull(
            $this->tieBreakersYamlRepository->getAllByYear(new Year(2019))
        );
    }
}
