<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class TranslatedAliases implements Collection
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
