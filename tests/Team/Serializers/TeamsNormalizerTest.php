<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class TeamsNormalizerTest extends TestCase
{
    /**
     * @var TeamsNormalizer
     */
    private $teamsNormalizer;

    protected function setUp(): void
    {
        $this->teamsNormalizer = new TeamsNormalizer(
            new TeamNormalizer()
        );
    }

    /**
     * @test
     */
    public function it_can_normalize_to_json(): void
    {
        $teams = ModelsFactory::createTeams();
        $actualJson = $this->teamsNormalizer->normalize(
            $teams,
            'json'
        );

        $this->assertEquals(
            ModelsFactory::createJson('teams'),
            json_encode($actualJson)
        );
    }

    /**
     * @test
     */
    public function it_supports_normalization(): void
    {
        $this->assertTrue(
            $this->teamsNormalizer->supportsNormalization(ModelsFactory::createTeams(), 'json')
        );
    }
}
