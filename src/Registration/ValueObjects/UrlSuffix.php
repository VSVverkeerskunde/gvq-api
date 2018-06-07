<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\ValueObjects;

class UrlSuffix
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private const MIN_LIMIT = 44;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (strlen($value) < self::MIN_LIMIT) {
            throw new \InvalidArgumentException(
                'Value has to be at least '.self::MIN_LIMIT.' characters long, '.strlen($value).' given.'
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
     * @param UrlSuffix $suffix
     * @return bool
     */
    public function equals(UrlSuffix $suffix): bool
    {
        return $this->toNative() === $suffix->toNative();
    }
}
