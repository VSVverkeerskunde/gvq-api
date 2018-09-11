<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

class NaturalNumber
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
        if ($value < 0) {
            throw new \InvalidArgumentException(
                'Value has to be 0 or greater, '.$value.' given.'
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
     * @param NaturalNumber $naturalNumber
     * @return bool
     */
    public function equals(NaturalNumber $naturalNumber): bool
    {
        return $this->toNative() === $naturalNumber->toNative();
    }
}
