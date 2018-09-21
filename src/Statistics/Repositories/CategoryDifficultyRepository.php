<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

interface CategoryDifficultyRepository
{
    /**
     * @param Category $category
     * @param Language $language
     */
    public function increment(Category $category, Language $language): void;

    /**
     * @param Category $category
     * @param Language $language
     * @return NaturalNumber
     */
    public function getCount(Category $category, Language $language): NaturalNumber;
}
