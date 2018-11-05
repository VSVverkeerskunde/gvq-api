<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

class Ttl
{
    /**
     * @var int
     */
    private $value;

    /**
     * Create a time to life in seconds.
     * @param int $value
     */
    public function __construct(int $value)
    {
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
