<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

class Languages implements \IteratorAggregate, \Countable
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
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->languages);
    }

    /**
     * @return Language[]
     */
    public function toArray(): array
    {
        return $this->languages;
    }
}
