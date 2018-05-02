<?php

namespace VSV\GVQ_API\Question;

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
                'Given language ' . $value . ' is not supported, only nl en fr are allowed.'
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
     * @return string
     */
    public function __toString(): string
    {
        return $this->toNative();
    }
}
