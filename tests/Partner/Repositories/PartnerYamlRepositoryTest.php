<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Repositories;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\ValueObjects\Year;

class PartnerYamlRepositoryTest extends TestCase
{
    /**
     * @var PartnerYamlRepository
     */
    private $partnerYamlRepository;

    /**
     * @var Partner
     */
    private $partner;

    protected function setUp(): void
    {
        $this->partnerYamlRepository = new PartnerYamlRepository(
            __DIR__.'/../../Factory/Samples/partners.yaml'
        );

        $this->partner = ModelsFactory::createNBPartner();
    }

    /**
     * @test
     */
    public function it_can_get_a_partner_by_alias_and_year(): void
    {
        $foundPartner = $this->partnerYamlRepository->getByYearAndAlias(
            new Year(2018),
            new Alias('nieuwsblad')
        );

        $this->assertEquals(
            ModelsFactory::createNBPartner(),
            $foundPartner
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_alias_does_not_exist(): void
    {
        $foundPartner = $this->partnerYamlRepository->getByYearAndAlias(
            new Year(2018),
            new Alias('wrongalias')
        );

        $this->assertNull($foundPartner);
    }

    /**
     * @test
     */
    public function it_returns_null_when_year_is_not_present(): void
    {
        $foundPartner = $this->partnerYamlRepository->getByYearAndAlias(
            new Year(2019),
            new Alias('nieuwsblad')
        );

        $this->assertNull($foundPartner);
    }
}
