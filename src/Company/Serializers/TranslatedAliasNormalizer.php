<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Company\Models\TranslatedAlias;

class TranslatedAliasNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($translatedAlias, $format = null, array $context = array()): array
    {
        /** @var TranslatedAlias $translatedAlias */
        return [
            'id' => $translatedAlias->getId()->toString(),
            'language' => $translatedAlias->getLanguage()->toNative(),
            'alias' => $translatedAlias->getAlias()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof TranslatedAlias) && ($format === 'json');
    }
}
