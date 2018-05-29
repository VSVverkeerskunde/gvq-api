<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

class Language
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string[]
     */
    private $supportedLanguages = [
        'nl',
        'fr',
    ];

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!in_array($value, $this->supportedLanguages)) {
            throw new \InvalidArgumentException(
                'Given language '.$value.' is not supported, only nl en fr are allowed.'
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
     * @param Language $language
     *
     * @return bool
     */
    public function equals(Language $language): bool
    {
        return $this->toNative() === $language->toNative();
    }
}