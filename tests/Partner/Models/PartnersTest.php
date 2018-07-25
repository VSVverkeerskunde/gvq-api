<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class PartnersTest extends TestCase
{
    /**
     * @var Partner[]
     */
    private $partnersArray;

    /**
     * @var Partners
     */
    private $partners;

    protected function setUp(): void
    {
        $this->partnersArray = [
            ModelsFactory::createNBPartner(),
            ModelsFactory::createDatsPartner(),
        ];
        
        $this->partners = new Partners(...$this->partnersArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_partners(): void
    {
        $actualPartners = [];
        foreach ($this->partners as $partner) {
            $actualPartners[] = $partner;
        }

        $this->assertEquals($this->partnersArray, $actualPartners);
    }
    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(2, count($this->partners));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->partnersArray,
            $this->partners->toArray()
        );
    }
}
