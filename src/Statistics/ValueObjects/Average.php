<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

class Average
{
    /**
     * @var float
     */
    private $value;

    /**
     * Average constructor.
     * @param float $value
     */
    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Value should be positive.');
        }

        $this->value = $value;
    }

    /**
     * @return float
     */
    public function toNative(): float
    {
        return $this->value;
    }
}
