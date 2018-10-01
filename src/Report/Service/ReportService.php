<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\Service;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Report\ValueObjects\CategoryPercentage;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\Repositories\CategoryDifficultyRepository;
use VSV\GVQ_API\Statistics\Repositories\QuestionDifficultyRepository;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\Statistics\ValueObjects\Percentage;

class ReportService
{
    /**
     * @var QuestionDifficultyRepository
     */
    private $questionDifficultyRepository;

    /**
     * @var CategoryDifficultyRepository
     */
    private $categoryCorrectRepository;

    /**
     * @var CategoryDifficultyRepository
     */
    private $categoryInCorrectRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param QuestionDifficultyRepository $questionDifficultyRepository
     * @param CategoryDifficultyRepository $categoryCorrectRepository
     * @param CategoryDifficultyRepository $categoryInCorrectRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        QuestionDifficultyRepository $questionDifficultyRepository,
        CategoryDifficultyRepository $categoryCorrectRepository,
        CategoryDifficultyRepository $categoryInCorrectRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->questionDifficultyRepository = $questionDifficultyRepository;
        $this->categoryCorrectRepository = $categoryCorrectRepository;
        $this->categoryInCorrectRepository = $categoryInCorrectRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Language $language
     * @return QuestionDifficulties
     */
    public function getCorrectQuestions(Language $language): QuestionDifficulties
    {
        return $this->questionDifficultyRepository->getBestRange(
            $language,
            new NaturalNumber(4)
        );
    }

    /**
     * @param Language $language
     * @return QuestionDifficulties
     */
    public function getInCorrectQuestions(Language $language): QuestionDifficulties
    {
        return $this->questionDifficultyRepository->getWorstRange(
            $language,
            new NaturalNumber(4)
        );
    }

    /**
     * @param Language $language
     * @return CategoryPercentage[]
     */
    public function getCategoriesPercentages(Language $language): array
    {
        $categories = $this->categoryRepository->getAll();

        if (empty($categories)) {
            return [];
        }

        $percentages = [];
        foreach ($categories as $category) {
            $percentages[] = $this->getCategoryPercentage(
                $category,
                $language
            );
        }

        return $percentages;
    }

    /**
     * @param Category $category
     * @param Language $language
     * @return CategoryPercentage
     */
    private function getCategoryPercentage(
        Category $category,
        Language $language
    ): CategoryPercentage {
        $correctCount = $this->categoryCorrectRepository->getCount(
            $category,
            $language
        )->toNative();

        $inCorrectCount = $this->categoryInCorrectRepository->getCount(
            $category,
            $language
        )->toNative();

        $divider = $correctCount + $inCorrectCount;
        $percentage = $divider !== 0 ? (float)$correctCount/(float)$divider : 0.0;

        return new CategoryPercentage(
            $category,
            $language,
            new Percentage($percentage)
        );
    }
}
