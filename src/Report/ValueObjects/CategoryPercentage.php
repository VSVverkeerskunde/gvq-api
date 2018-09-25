<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Category;

class CategoryPercentage
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var float
     */
    private $percentage;

    /**
     * @param Category $category
     * @param Language $language
     * @param float $percentage
     */
    public function __construct(
        Category $category,
        Language $language,
        float $percentage
    ) {
        $this->category = $category;
        $this->language = $language;
        $this->percentage = $percentage;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return float
     */
    public function getPercentage(): float
    {
        return $this->percentage;
    }
}
