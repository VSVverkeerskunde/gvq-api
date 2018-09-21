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

class ReportService
{
    /**
     * @var QuestionDifficultyRepository
     */
    private $questionCorrectRepository;

    /**
     * @var QuestionDifficultyRepository
     */
    private $questionInCorrectRepository;

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
     * @param QuestionDifficultyRepository $questionCorrectRepository
     * @param QuestionDifficultyRepository $questionInCorrectRepository
     * @param CategoryDifficultyRepository $categoryCorrectRepository
     * @param CategoryDifficultyRepository $categoryInCorrectRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        QuestionDifficultyRepository $questionCorrectRepository,
        QuestionDifficultyRepository $questionInCorrectRepository,
        CategoryDifficultyRepository $categoryCorrectRepository,
        CategoryDifficultyRepository $categoryInCorrectRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->questionCorrectRepository = $questionCorrectRepository;
        $this->questionInCorrectRepository = $questionInCorrectRepository;
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
        return $this->questionCorrectRepository->getRange(
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
        return $this->questionInCorrectRepository->getRange(
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

        $inCorrectCount = $this->categoryCorrectRepository->getCount(
            $category,
            $language
        )->toNative();

        return new CategoryPercentage(
            $category,
            $language,
            round(
                $correctCount / $correctCount + $inCorrectCount,
                2
            )
        );
    }
}
