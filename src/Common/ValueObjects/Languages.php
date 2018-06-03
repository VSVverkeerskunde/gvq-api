<?php

namespace VSV\GVQ_API\Common\ValueObjects;

class Languages implements \IteratorAggregate
{
    /**
     * @var Language[]
     */
    private $languages;

    public function __construct()
    {
        $this->languages = [
            new Language('nl'),
            new Language('fr')
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
{
    return new \ArrayIterator($this->languages);
}

    /**
     * @return Language[]
     */
    public function toArray(): array
    {
        return $this->languages;
    }
}
