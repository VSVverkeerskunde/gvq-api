<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics;

use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;

class EmployeeParticipationShare
{
    /**
     * @var PositiveNumber
     */
    private $totalEmployees;

    /**
     * @var int
     */
    private $participationCount;

    /**
     * @param PositiveNumber $totalEmployees
     * @param int $participationCount
     */
    public function __construct(int $participationCount, PositiveNumber $totalEmployees)
    {
        $this->totalEmployees = $totalEmployees;
        $this->participationCount = $participationCount;
    }

    /**
     * @return PositiveNumber
     */
    public function getTotalEmployees(): PositiveNumber
    {
        return $this->totalEmployees;
    }

    /**
     * @return int
     */
    public function getParticipationCount(): int
    {
        return $this->participationCount;
    }
}
