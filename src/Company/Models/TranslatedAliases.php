<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;
use VSV\GVQ_API\Common\ValueObjects\Language;

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

    /**
     * @param Language $language
     * @return TranslatedAlias
     */
    public function getByLanguage(Language $language): TranslatedAlias
    {
        $foundAlias = $this->translatedAliases[0];

        foreach ($this->translatedAliases as $translatedAlias) {
            if ($translatedAlias->getLanguage()->equals($language)) {
                $foundAlias = $translatedAlias;
            }
        }

        return $foundAlias;
    }
}
