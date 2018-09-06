<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;

class EmployeeParticipationRatio
{
    /**
     * @var PositiveNumber
     */
    private $totalEmployees;

    /**
     * @var NaturalNumber
     */
    private $participationCount;

    /**
     * @param PositiveNumber $totalEmployees
     * @param NaturalNumber $participationCount
     */
    public function __construct(NaturalNumber $participationCount, PositiveNumber $totalEmployees)
    {
        if ($participationCount->toNative() < 0) {
            throw new \InvalidArgumentException('participation count has to be at least zero');
        }

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
     * @return NaturalNumber
     */
    public function getParticipationCount(): NaturalNumber
    {
        return $this->participationCount;
    }
}
