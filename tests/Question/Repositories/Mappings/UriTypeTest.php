<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories\Mappings;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use League\Uri\Uri;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class UriTypeTest extends TestCase
{
    /**
     * @var Type
     */
    private $uriType;

    /**
     * @var AbstractPlatform
     */
    private $platform;

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp(): void
    {
        if (!Type::hasType('league_uri')) {
            Type::addType('league_uri', UriType::class);
        }

        $this->uriType = Type::getType('league_uri');

        $this->platform =  new MySqlPlatform();
    }

    /**
     * @test
     */
    public function it_has_sql_type_string()
    {
        $this->assertEquals(
            'VARCHAR(255)',
            $this->uriType->getSQLDeclaration(
                [],
                $this->platform
            )
        );
    }

    /**
     * @test
     */
    public function it_has_name_league_uri()
    {
        $this->assertEquals(
            'league_uri',
            $this->uriType->getName()
        );
    }

    /**
     * @test
     */
    public function it_converts_to_php_type_uri()
    {
        $uri = Uri::createFromString('https://github.com/VSVverkeerskunde/gvq-api');
        // @see https://github.com/thephpleague/uri-schemes/issues/10
        $uri->__toString();

        $this->assertEquals(
            $uri,
            $this->uriType->convertToPHPValue(
                'https://github.com/VSVverkeerskunde/gvq-api',
                $this->platform
            )
        );
    }

    /**
     * @test
     */
    public function it_converts_to_sql_type_string()
    {
        $this->assertEquals(
            'https://github.com/VSVverkeerskunde/gvq-api',
            $this->uriType->convertToDatabaseValue(
                Uri::createFromString('https://github.com/VSVverkeerskunde/gvq-api'),
                $this->platform
            )
        );
    }

    /**
     * @test
     */
    public function it_throws_on_conversion_to_sql_type_string_when_not_uri()
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            'Could not convert PHP value of type \''.NotEmptyString::class.
            '\' to type \''.Uri::class.
            '\'. Expected one of the following types: '.Uri::class
        );

        $this->assertEquals(
            'https://github.com/VSVverkeerskunde/gvq-api',
            $this->uriType->convertToDatabaseValue(
                new NotEmptyString('https://github.com/VSVverkeerskunde/gvq-api'),
                $this->platform
            )
        );
    }
}
