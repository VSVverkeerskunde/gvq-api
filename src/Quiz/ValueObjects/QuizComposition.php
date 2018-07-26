<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuizComposition
{

    /**
     * @var Year
     */
    private $year;

    /**
     * @var array
     */
    private $categoryCounts;

    /**
     * @param Year $year
     * @param array $categoryCounts
     */
    public function __construct(Year $year, array $categoryCounts)
    {
        $this->year = $year;
        $this->categoryCounts = $categoryCounts;
    }

    public function getCategoryCount(Category $category): int
    {
        return $this->categoryCounts[$category->getId()->toString()];
    }
}
