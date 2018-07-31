<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\Year;

interface QuizCompositionRepository
{
    /**
     * @param Year $year
     * @param Category $category
     * @return int|null
     */
    public function getCountByYearAndCategory(Year $year, Category $category): ?int;
}
