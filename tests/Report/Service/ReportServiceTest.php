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

class ReportServiceTest extends TestCase
{
    /**
     * @var QuestionDifficultyRepository|MockObject
     */
    private $questionCorrectRepository;

    /**
     * @var QuestionDifficultyRepository|MockObject
     */
    private $questionInCorrectRepository;

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
        /** @var QuestionDifficultyRepository|MockObject $questionCorrectRepository */
        $questionCorrectRepository = $this->createMock(QuestionDifficultyRepository::class);
        $this->questionCorrectRepository = $questionCorrectRepository;

        /** @var QuestionDifficultyRepository|MockObject $questionInCorrectRepository */
        $questionInCorrectRepository = $this->createMock(QuestionDifficultyRepository::class);
        $this->questionInCorrectRepository = $questionInCorrectRepository;

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
            $this->questionCorrectRepository,
            $this->questionInCorrectRepository,
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
                new NaturalNumber(2)
            )
        );

        $this->questionCorrectRepository->expects($this->once())
            ->method('getRange')
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
                new NaturalNumber(2)
            )
        );

        $this->questionInCorrectRepository->expects($this->once())
            ->method('getRange')
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
                    70.0
                ),
                new CategoryPercentage(
                    ModelsFactory::createGeneralCategory(),
                    new Language(Language::NL),
                    80.0
                )
            ],
            $this->reportService->getCategoriesPercentages(
                new Language(Language::NL)
            )
        );
    }
}
