<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Category;

class CategoryNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var Category $object */
        return [
            'id' => $object->getId()->toString(),
            'name' => $object->getName()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof Category) && ($format === 'json');
    }
}
