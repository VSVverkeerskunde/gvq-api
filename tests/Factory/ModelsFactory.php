<?php declare(strict_types=1);

namespace VSV\GVQ_API\Factory;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class ModelsFactory
{
    /**
     * @return Company
     */
    public static function createCompany(): Company
    {
        return new Company(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            new NotEmptyString('Company Name'),
            self::createTranslatedAliases()
        );
    }

    /**
     * @return TranslatedAliases
     */
    public static function createTranslatedAliases(): TranslatedAliases
    {
        return new TranslatedAliases(
            self::createNlAlias(),
            self::createFrAlias()
        );
    }

    /**
     * @return TranslatedAlias
     */
    public static function createNlAlias(): TranslatedAlias
    {
        return new TranslatedAlias(
            Uuid::fromString('827a7945-ffd0-433e-b843-721c98ab72b8'),
            new Language('nl'),
            new Alias('company-name-nl')
        );
    }

    /**
     * @return TranslatedAlias
     */
    public static function createFrAlias(): TranslatedAlias
    {
        return new TranslatedAlias(
            Uuid::fromString('f99c7747-7c27-4388-b0ec-dba380363d23'),
            new Language('fr'),
            new Alias('company-name-fr')
        );
    }
}
