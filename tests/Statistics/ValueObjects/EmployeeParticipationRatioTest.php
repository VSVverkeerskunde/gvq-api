<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;

class EmployeeParticipationRatioTest extends TestCase
{
    /**
     * @var EmployeeParticipationRatio
     */
    private $employeeParticipationRatio;

    protected function setUp(): void
    {
        $this->employeeParticipationRatio = new EmployeeParticipationRatio(
            new NaturalNumber(10),
            new PositiveNumber(15)
        );
    }

    /**
     * @test
     */
    public function it_stores_a_participation_count(): void
    {
        $this->assertEquals(
            new NaturalNumber(10),
            $this->employeeParticipationRatio->getParticipationCount()
        );
    }

    /**
     * @test
     */
    public function it_stores_total_number_of_employees(): void
    {
        $this->assertEquals(
            new PositiveNumber(15),
            $this->employeeParticipationRatio->getTotalEmployees()
        );
    }
}
