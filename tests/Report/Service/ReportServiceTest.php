<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Report\ValueObjects\CategoryPercentage;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulty;
use VSV\GVQ_API\Statistics\Repositories\CategoryDifficultyRepository;
use VSV\GVQ_API\Statistics\Repositories\QuestionDifficultyRepository;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\Statistics\ValueObjects\Percentage;

class ReportServiceTest extends TestCase
{
    /**
     * @var QuestionDifficultyRepository|MockObject
     */
    private $questionDifficultyRepository;

    /**
     * @var CategoryDifficultyRepository|MockObject
     */
    private $categoryCorrectRepository;

    /**
     * @var CategoryDifficultyRepository|MockObject
     */
    private $categoryInCorrectRepository;

    /**
     * @var CategoryRepository|MockObject
     */
    private $categoryRepository;

    /**
     * @var ReportService
     */
    private $reportService;

    protected function setUp(): void
    {
        /** @var QuestionDifficultyRepository|MockObject $questionDifficultyRepository */
        $questionDifficultyRepository = $this->createMock(QuestionDifficultyRepository::class);
        $this->questionDifficultyRepository = $questionDifficultyRepository;

        /** @var CategoryDifficultyRepository|MockObject $categoryCorrectRepository */
        $categoryCorrectRepository = $this->createMock(CategoryDifficultyRepository::class);
        $this->categoryCorrectRepository = $categoryCorrectRepository;

        /** @var CategoryDifficultyRepository|MockObject $categoryInCorrectRepository */
        $categoryInCorrectRepository = $this->createMock(CategoryDifficultyRepository::class);
        $this->categoryInCorrectRepository = $categoryInCorrectRepository;

        /** @var CategoryRepository|MockObject $categoryRepository */
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $this->categoryRepository = $categoryRepository;

        $this->reportService = new ReportService(
            $this->questionDifficultyRepository,
            $this->categoryCorrectRepository,
            $this->categoryInCorrectRepository,
            $this->categoryRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_correct_questions(): void
    {
        $questionDifficulties = new QuestionDifficulties(
            new QuestionDifficulty(
                ModelsFactory::createAccidentQuestion(),
                new Percentage(0.66)
            )
        );

        $this->questionDifficultyRepository->expects($this->once())
            ->method('getBestRange')
            ->with(
                new Language(Language::FR),
                new NaturalNumber(4)
            )
            -> willReturn(
                $questionDifficulties
            );

        $this->assertEquals(
            $questionDifficulties,
            $this->reportService->getCorrectQuestions(
                new Language(Language::FR)
            )
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_get_incorrect_questions(): void
    {
        $questionDifficulties = new QuestionDifficulties(
            new QuestionDifficulty(
                ModelsFactory::createAccidentQuestion(),
                new Percentage(0.24)
            )
        );

        $this->questionDifficultyRepository->expects($this->once())
            ->method('getWorstRange')
            ->with(
                new Language(Language::FR),
                new NaturalNumber(4)
            )
            -> willReturn(
                $questionDifficulties
            );

        $this->assertEquals(
            $questionDifficulties,
            $this->reportService->getInCorrectQuestions(
                new Language(Language::FR)
            )
        );
    }

    /**
     * @test
     */
    public function it_can_get_categories_percentages(): void
    {
        $this->categoryRepository->expects($this->once())
            ->method('getAll')
            ->willReturn(
                ModelsFactory::createCategories()
            );

        $this->categoryCorrectRepository->expects($this->exactly(2))
            ->method('getCount')
            ->willReturnOnConsecutiveCalls(
                new NaturalNumber(7),
                new NaturalNumber(8)
            );

        $this->categoryInCorrectRepository->expects($this->exactly(2))
            ->method('getCount')
            ->willReturnOnConsecutiveCalls(
                new NaturalNumber(3),
                new NaturalNumber(2)
            );

        $this->assertEquals(
            [
                new CategoryPercentage(
                    ModelsFactory::createAccidentCategory(),
                    new Language(Language::NL),
                    new Percentage(0.70)
                ),
                new CategoryPercentage(
                    ModelsFactory::createGeneralCategory(),
                    new Language(Language::NL),
                    new Percentage(0.80)
                ),
            ],
            $this->reportService->getCategoriesPercentages(
                new Language(Language::NL)
            )
        );
    }

    /**
     * @test
     */
    public function it_return_empty_array_when_no_categories(): void
    {
        $this->categoryRepository->expects($this->once())
            ->method('getAll')
            ->willReturn(null);

        $this->assertEquals(
            [],
            $this->reportService->getCategoriesPercentages(
                new Language(Language::NL)
            )
        );
    }
}
