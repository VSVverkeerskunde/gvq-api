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
        if ($aliases->count() !== 2 || !$this->hasValidValues($aliases)) {
            $suppliedValues = '';
            foreach ($aliases as $alias) {
                $suppliedValues .= $alias->getAlias()->toNative().' - '.$alias->getLanguage()->toNative().', ';
            }

            throw new \InvalidArgumentException(
                'Invalid value(s) for aliases: '.$suppliedValues.
                'exactly one alias per language (nl and fr) required.'
            );
        }
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
     * @return bool
     */
    public function hasValidValues(TranslatedAliases $aliases): bool
    {
        $languages = [];
        foreach ($aliases as $alias) {
            $languages[] = $alias->getLanguage()->toNative();
        }

        $freqs = array_count_values($languages);
        if ($freqs['nl'] === 1 && $freqs['fr'] === 1) {
            return true;
        }

        return false;
    }
}
