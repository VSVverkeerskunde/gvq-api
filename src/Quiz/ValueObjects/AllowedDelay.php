<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

class AllowedDelay
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
        if ($value <= 0) {
            throw new \InvalidArgumentException('Allowed delay should be a positive number.');
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
}
