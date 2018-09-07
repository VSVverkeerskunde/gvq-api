<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;

class AddressTest extends TestCase
{
    /**
     * @var Address
     */
    private $vsvAddress;

    protected function setUp(): void
    {
        $this->vsvAddress = ModelsFactory::createVsvAddress();
    }

    /**
     * @test
     */
    public function it_stores_a_street(): void
    {
        $this->assertEquals(
            new NotEmptyString('Stationsstraat'),
            $this->vsvAddress->getStreet()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_number(): void
    {
        $this->assertEquals(
            new NotEmptyString('110'),
            $this->vsvAddress->getNumber()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_postal_code(): void
    {
        $this->assertEquals(
            new NotEmptyString('2800'),
            $this->vsvAddress->getPostalCode()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_town(): void
    {
        $this->assertEquals(
            new NotEmptyString('Mechelen'),
            $this->vsvAddress->getTown()
        );
    }
}
