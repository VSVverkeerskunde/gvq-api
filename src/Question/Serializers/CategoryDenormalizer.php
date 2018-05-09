<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class CategoryDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = []): Category
    {
        return new Category(
            Uuid::fromString($data['id']),
            new NotEmptyString($data['name'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Category::class) && ($format === 'json');
    }
}
