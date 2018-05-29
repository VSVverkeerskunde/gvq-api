<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\TranslatedAlias;

class TranslatedAliasDenormalizerTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     * @param TranslatedAlias|NotEmptyString $data
     * @param string $type
     * @param string $format
     */
    public function it_only_supports_translated_alias_type_and_json_format(
        $data,
        string $type,
        string $format
    ): void {
        $translatedAliasDenormalizer = new TranslatedAliasDenormalizer();

        $this->assertFalse(
            $translatedAliasDenormalizer->supportsDenormalization(
                $data,
                $type,
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
                [],
                TranslatedAlias::class,
                'xml',
            ],
            [
                [],
                NotEmptyString::class,
                'json',
            ],
        ];
    }
}
