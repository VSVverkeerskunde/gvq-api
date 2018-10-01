<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

class Percentage
{
    /**
     * @var float
     */
    private $value;

    /**
     * @param float $value
     */
    public function __construct(float $value)
    {
        if ($value < 0 || $value > 1) {
            throw new \InvalidArgumentException(
                'Value should be between 0 and 1. Given value: '.$value
            );
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

    /**
     * @return NaturalNumber
     */
    public function toPercentage(): NaturalNumber
    {
        return new NaturalNumber((int)($this->value * 100));
    }
}
