<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulty;
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

        $this->reportService = new ReportService(
            $this->questionCorrectRepository,
            $this->questionInCorrectRepository
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
}
