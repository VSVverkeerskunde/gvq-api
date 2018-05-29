<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Factory\ModelsFactory;

class TranslatedAliasNormalizerTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     * @param TranslatedAlias|NotEmptyString $data
     * @param string $format
     */
    public function it_only_supports_translated_alias_type_and_json_format(
        $data,
        string $format
    ): void {
        $translatedAliasNormalizer = new TranslatedAliasNormalizer();

        $this->assertFalse(
            $translatedAliasNormalizer->supportsNormalization(
                $data,
                $format
            )
        );
    }

    /**
     * @return array[]
     */
    public function dataProvider(): array
    {
        return [
            [
                ModelsFactory::createNlAlias(),
                'xml',
            ],
            [
                new NotEmptyString('This is a string'),
                'json',
            ],
        ];
    }
}
