<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class CompanyTest extends TestCase
{
    /**
     * @var Company
     */
    private $company;

    protected function setUp(): void
    {
        $this->company = new Company(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            new NotEmptyString('Company Name'),
            [
                new Alias('company-name-nl'),
                new Alias('company-name-fr'),
            ]
        );
    }

    /**
     * @test
     */
    public function it_can_store_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            $this->company->getId()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('Company Name'),
            $this->company->getName()
        );
    }

    /**
     * @test
     */
    public function it_can_store_aliases(): void
    {
        $this->assertEquals(
            [
                new Alias('company-name-nl'),
                new Alias('company-name-fr'),
            ],
            $this->company->getAliases()
        );
    }
}
