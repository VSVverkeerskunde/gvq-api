<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class CompaniesTest extends TestCase
{
    /**
     * @var Company[]
     */
    private $companiesArray;

    /**
     * @var Companies
     */
    private $companies;

    protected function setUp(): void
    {
        $this->companiesArray = [
            ModelsFactory::createCompany(),
            ModelsFactory::createAlternateCompany(),
        ];

        $this->companies = new Companies(...$this->companiesArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_companies(): void
    {
        $actualCompanies = [];
        foreach ($this->companies as $company) {
            $actualCompanies[] = $company;
        }

        $this->assertEquals($this->companiesArray, $actualCompanies);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(2, count($this->companies));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->companiesArray,
            $this->companies->toArray()
        );
    }
}
