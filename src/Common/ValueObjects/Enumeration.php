<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

abstract class Enumeration
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!in_array($value, $this->getAllowedValues())) {
            throw new \InvalidArgumentException(
                'Invalid value "'.$value.'" for '.get_class($this).'.'
            );
        }
        $this->value = $value;
    }

    /**
     * @return string[]
     */
    abstract public function getAllowedValues(): array;

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->value;
    }

    /**
     * @param Enumeration $enumeration
     * @return bool
     */
    public function equals(Enumeration $enumeration): bool
    {
        return $this->toNative() === $enumeration->toNative();
    }
}
