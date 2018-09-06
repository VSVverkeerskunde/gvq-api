<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class AverageScore
{
    /**
     * @var UuidInterface
     */
    private $companyId;

    /**
     * @var NaturalNumber
     */
    private $score;

    /**
     * @param UuidInterface $companyId
     * @param NaturalNumber $score
     */
    public function __construct(UuidInterface $companyId, NaturalNumber $score)
    {
        $this->companyId = $companyId;
        $this->score = $score;
    }

    /**
     * @return UuidInterface
     */
    public function getCompanyId(): UuidInterface
    {
        return $this->companyId;
    }

    /**
     * @return NaturalNumber
     */
    public function getScore(): NaturalNumber
    {
        return $this->score;
    }
}
