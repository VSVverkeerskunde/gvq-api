<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;

class CompanyNormalizer implements NormalizerInterface
{
    /**
     * @var TranslatedAliasNormalizer
     */
    private $translatedAliasNormalizer;

    /**
     * @param TranslatedAliasNormalizer $translatedAliasNormalizer
     */
    public function __construct(
        TranslatedAliasNormalizer $translatedAliasNormalizer
    ) {
        $this->translatedAliasNormalizer = $translatedAliasNormalizer;
    }

    /**
     * @inheritdoc
     */
    public function normalize($company, $format = null, array $context = array()): array
    {
        /** @var Company $company */

        $aliases = array_map(
            function (TranslatedAlias $translatedAlias) use ($format) {
                return $this->translatedAliasNormalizer->normalize(
                    $translatedAlias,
                    $format
                );
            },
            $company->getAliases()->toArray()
        );


        return [
            'id' => $company->getId()->toString(),
            'name' => $company->getName()->toNative(),
            'aliases' => $aliases,
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Company) && ($format === 'json');
    }
}
