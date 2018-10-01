<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Statistics\ValueObjects\Percentage;

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
     * @var Percentage
     */
    private $percentage;

    /**
     * @param Category $category
     * @param Language $language
     * @param Percentage $percentage
     */
    public function __construct(
        Category $category,
        Language $language,
        Percentage $percentage
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
     * @return Percentage
     */
    public function getPercentage(): Percentage
    {
        return $this->percentage;
    }
}
