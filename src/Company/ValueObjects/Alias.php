<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

class Alias
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
                'Invalid value: '.$value.' for TranslatedAlias. Value should be '.
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
     * @param Alias $aliasString
     * @return bool
     */
    public function equals(Alias $aliasString): bool
    {
        return $this->toNative() === $aliasString->toNative();
    }
}