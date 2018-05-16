<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

class AliasString
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
        if (!preg_match('/^[a-z0-9\-]{4,20}$/', $value)) {
            throw new \InvalidArgumentException(
                'Invalid value: '.$value.' for AliasString. Value should be '.
                'between 4 and 20 characters long and consist only of lowercase letters, numbers and "-"'
            );
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
     * @param AliasString $aliasString
     * @return bool
     */
    public function equals(AliasString $aliasString): bool
    {
        return $this->toNative() === $aliasString->toNative();
    }
}
