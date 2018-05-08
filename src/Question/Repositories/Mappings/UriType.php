<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories\Mappings;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use League\Uri\Uri;

class UriType extends Type
{
    private const NAME = 'league_uri';

    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $uri = Uri::createFromString($value);

        // Call toString method to solve side effect in Uri lib because the data
        // property gets created on calling this getter method.
        // @see https://github.com/thephpleague/uri-schemes/issues/10
        $uri->__toString();

        return $uri;
    }

    /**
     * @inheritdoc
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Uri) {
            return $value->__toString();
        }
        throw ConversionException::conversionFailedInvalidType(
            $value,
            Uri::class,
            [
                Uri::class,
            ]
        );
    }
}
