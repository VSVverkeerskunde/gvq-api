<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;

class EmployeeParticipationRatio
{
    /**
     * @var NaturalNumber
     */
    private $participationCount;

    /**
     * @var PositiveNumber
     */
    private $totalEmployees;

    /**
     * @param PositiveNumber $totalEmployees
     * @param NaturalNumber $participationCount
     */
    public function __construct(NaturalNumber $participationCount, PositiveNumber $totalEmployees)
    {
        $this->totalEmployees = $totalEmployees;
        $this->participationCount = $participationCount;
    }

    /**
     * @return NaturalNumber
     */
    public function getParticipationCount(): NaturalNumber
    {
        return $this->participationCount;
    }

    /**
     * @return PositiveNumber
     */
    public function getTotalEmployees(): PositiveNumber
    {
        return $this->totalEmployees;
    }
}
