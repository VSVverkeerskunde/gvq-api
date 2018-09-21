<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Statistics\Repositories\CategoryDifficultyRepository;

class CategoryDifficultyProjectorTest extends TestCase
{
    /**
     * @var CategoryDifficultyRepository|MockObject
     */
    private $categoryCorrectRepository;

    /**
     * @var CategoryDifficultyRepository|MockObject
     */
    private $categoryInCorrectRepository;

    /**
     * @var CategoryDifficultyProjector
     */
    private $categoryDifficultyProjector;

    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @var Question
     */
    private $question;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        /** @var CategoryDifficultyRepository|MockObject $categoryCorrectRepository */
        $categoryCorrectRepository = $this->createMock(CategoryDifficultyRepository::class);
        $this->categoryCorrectRepository = $categoryCorrectRepository;

        /** @var CategoryDifficultyRepository|MockObject $categoryInCorrectRepository */
        $categoryInCorrectRepository = $this->createMock(CategoryDifficultyRepository::class);
        $this->categoryInCorrectRepository = $categoryInCorrectRepository;

        $this->categoryDifficultyProjector = new CategoryDifficultyProjector(
            $this->categoryCorrectRepository,
            $this->categoryInCorrectRepository
        );

        $this->quiz = ModelsFactory::createCompanyQuiz();
        $this->question = $this->quiz->getQuestions()->getIterator()->current();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_handle_answered_correct(): void
    {
        $answeredCorrectDomainMessage = ModelsFactory::createAnsweredCorrectDomainMessage(
            $this->quiz,
            $this->question
        );

        $this->categoryCorrectRepository->expects($this->once())
            ->method('increment')
            ->with(
                $this->question->getCategory(),
                $this->question->getLanguage()
            );

        $this->categoryInCorrectRepository->expects($this->never())
            ->method('increment');

        $this->categoryDifficultyProjector->handle($answeredCorrectDomainMessage);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_handle_answered_incorrect(): void
    {
        $answeredCorrectDomainMessage = ModelsFactory::createAnsweredInCorrectDomainMessage(
            $this->quiz,
            $this->question
        );

        $this->categoryCorrectRepository->expects($this->never())
            ->method('increment')
            ->with(
                $this->question->getCategory(),
                $this->question->getLanguage()
            );

        $this->categoryInCorrectRepository->expects($this->once())
            ->method('increment')
            ->with(
                $this->question->getCategory(),
                $this->question->getLanguage()
            );

        $this->categoryDifficultyProjector->handle($answeredCorrectDomainMessage);
    }
}
