<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use Ramsey\Uuid\UuidInterface;

class AverageScore
{
    /**
     * @var UuidInterface
     */
    private $companyId;

    /**
     * @var Average
     */
    private $score;

    /**
     * @param UuidInterface $companyId
     * @param Average $score
     */
    public function __construct(UuidInterface $companyId, Average $score)
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
     * @return Average
     */
    public function getScore(): Average
    {
        return $this->score;
    }
}
