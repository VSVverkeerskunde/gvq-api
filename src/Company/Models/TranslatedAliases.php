<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

class TranslatedAliases implements \IteratorAggregate, \Countable
{
    /**
     * @var TranslatedAlias[]
     */
    private $translatedAliases;

    /**
     * @param TranslatedAlias ...$translatedAliases
     */
    public function __construct(TranslatedAlias ...$translatedAliases)
    {
        $this->translatedAliases = $translatedAliases;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->translatedAliases);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->translatedAliases);
    }

    /**
     * @return TranslatedAlias[]
     */
    public function toArray(): array
    {
        return $this->translatedAliases;
    }
}
