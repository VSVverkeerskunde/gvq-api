<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

class NotEmptyString
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
        $value = trim($value);
        if ($value === '') {
            throw new \InvalidArgumentException('The string value cannot be empty.');
        }
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->value;
    }

    /**
     * @param NotEmptyString $notEmptyString
     * @return bool
     */
    public function equals(NotEmptyString $notEmptyString): bool
    {
        return $this->toNative() === $notEmptyString->toNative();
    }
}
