<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\User\Models\User;

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
     * @var PositiveNumber
     */
    private $numberOfEmployees;

    /**
     * @var TranslatedAliases
     */
    private $translatedAliases;

    /**
     * @var User
     */
    private $user;

    /**
     * @param UuidInterface $id
     * @param NotEmptyString $name
     * @param PositiveNumber $numberOfEmployees
     * @param TranslatedAliases $translatedAliases
     * @param User $user
     */
    public function __construct(
        UuidInterface $id,
        NotEmptyString $name,
        PositiveNumber $numberOfEmployees,
        TranslatedAliases $translatedAliases,
        User $user
    ) {
        $this->guardTranslatedAliases($translatedAliases);

        $this->id = $id;
        $this->name = $name;
        $this->numberOfEmployees = $numberOfEmployees;
        $this->translatedAliases = $translatedAliases;
        $this->user = $user;
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
     * @return PositiveNumber
     */
    public function getNumberOfEmployees(): PositiveNumber
    {
        return $this->numberOfEmployees;
    }

    /**
     * @return TranslatedAliases
     */
    public function getTranslatedAliases(): TranslatedAliases
    {
        return $this->translatedAliases;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
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

        if ($aliases->count() !== 2 || $languageCount[Language::NL] !== 1 || $languageCount[Language::FR] !== 1) {
            $suppliedAliases = [];
            foreach ($aliases as $alias) {
                $suppliedAliases[] = $alias->getAlias()->toNative().' ('.$alias->getLanguage()->toNative().')';
            }

            throw new \InvalidArgumentException(
                'Invalid value(s) for aliases: '.implode(', ', $suppliedAliases).
                '. Exactly one alias per language (nl and fr) required.'
            );
        }
    }
}
