<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

class PositiveNumber
{

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException(
                'Value has to be greater than 0, '.$value.' given.'
            );
        }
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function toNative(): int
    {
        return $this->value;
    }

    /**
     * @param PositiveNumber $positiveNumber
     * @return bool
     */
    public function equals(PositiveNumber $positiveNumber): bool
    {
        return $this->toNative() === $positiveNumber->toNative();
    }
}
