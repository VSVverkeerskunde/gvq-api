<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;

class CategoriesNormalizer implements NormalizerInterface
{
    /**
     * @var CategoryNormalizer
     */
    private $categoryNormalizer;

    /**
     * @param CategoryNormalizer $categoryNormalizer
     */
    public function __construct(CategoryNormalizer $categoryNormalizer)
    {
        $this->categoryNormalizer = $categoryNormalizer;
    }

    /**
     * @inheritdoc
     * @param Categories $categories
     */
    public function normalize($categories, $format = null, array $context = []): array
    {
        return array_map(
            function (Category $category) use ($format, $context) {
                return $this->categoryNormalizer->normalize($category, $format, $context);
            },
            $categories->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Categories) && ($format === 'json');
    }
}
