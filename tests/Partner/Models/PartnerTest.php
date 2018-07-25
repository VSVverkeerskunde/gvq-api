<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Factory\ModelsFactory;

class PartnerTest extends TestCase
{
    /**
     * @var Partner
     */
    private $partner;

    protected function setUp(): void
    {
        $this->partner = ModelsFactory::createNBPartner();
    }

    /**
     * @test
     */
    public function it_can_store_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('b00bfa30-97e4-4972-bd65-24b371f75718'),
            $this->partner->getId()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('Nieuwsblad'),
            $this->partner->getName()
        );
    }

    /**
     * @test
     */
    public function it_can_store_an_alias(): void
    {
        $this->assertEquals(
            new Alias('nieuwsblad'),
            $this->partner->getAlias()
        );
    }
}
