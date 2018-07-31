<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

class Alias
{
    const PATTERN = '/^[a-z0-9\-]{3,40}$/';

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!preg_match(self::PATTERN, $value)) {
            throw new \InvalidArgumentException(
                'Invalid value: '.$value.' for TranslatedAlias. Value should be '.
                'between 3 and 40 characters long and consist only of lowercase letters, numbers and "-"'
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
