<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Category;

class CategoryNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param Category $category
     */
    public function normalize($category, $format = null, array $context = []): array
    {
        return [
            'id' => $category->getId()->toString(),
            'name' => $category->getName()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Category) && ($format === 'json');
    }
}
