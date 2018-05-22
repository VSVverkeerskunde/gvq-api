<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class Company
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var NotEmptyString
     */
    private $name;

    /**
     * @var TranslatedAliases
     */
    private $aliases;

    /**
     * @param UuidInterface $id
     * @param NotEmptyString $name
     * @param TranslatedAliases $aliases
     */
    public function __construct(UuidInterface $id, NotEmptyString $name, TranslatedAliases $aliases)
    {
        $this->guardTranslatedAliases($aliases);

        $this->id = $id;
        $this->name = $name;
        $this->aliases = $aliases;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return NotEmptyString
     */
    public function getName(): NotEmptyString
    {
        return $this->name;
    }

    /**
     * @return TranslatedAliases
     */
    public function getAliases(): TranslatedAliases
    {
        return $this->aliases;
    }

    /**
     * @param TranslatedAliases $aliases
     */
    private function guardTranslatedAliases(TranslatedAliases $aliases): void
    {
        $languages = [];
        foreach ($aliases as $alias) {
            $languages[] = $alias->getLanguage()->toNative();
        }
        $languageCount = array_count_values($languages);

        if ($aliases->count() !== 2 || !($languageCount['nl'] === 1 && $languageCount['fr'] === 1)) {
            $suppliedAliases = [];
            foreach ($aliases as $alias) {
                $suppliedAliases[] = $alias->getAlias()->toNative().' - '.$alias->getLanguage()->toNative();
            }

            throw new \InvalidArgumentException(
                'Invalid value(s) for aliases: '.implode($suppliedAliases).
                'exactly one alias per language (nl and fr) required.'
            );
        }
    }
}
