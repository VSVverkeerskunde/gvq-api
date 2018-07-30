<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuizCompositionYamlRepositoryTest extends TestCase
{
    /**
     * @var QuizCompositionYamlRepository
     */
    private $quizCompositionYamlRepository;

    protected function setUp(): void
    {
        $this->quizCompositionYamlRepository = new QuizCompositionYamlRepository(
            __DIR__.'/../../Factory/Samples/quiz_composition.yaml'
        );
    }

    /**
     * @test
     */
    public function it_can_get_question_count_by_year_and_category(): void
    {
        $foundQuestionCount = $this->quizCompositionYamlRepository->getCountByYearAndCategory(
            new Year(2018),
            ModelsFactory::createGeneralCategory()
        );

        $this->assertEquals(
            2,
            $foundQuestionCount
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_year_is_not_present(): void
    {
        $foundQuestionCount = $this->quizCompositionYamlRepository->getCountByYearAndCategory(
            new Year(2019),
            ModelsFactory::createGeneralCategory()
        );

        $this->assertNull($foundQuestionCount);
    }

    /**
     * @test
     */
    public function it_returns_null_when_category_is_not_present(): void
    {
        $foundQuestionCount = $this->quizCompositionYamlRepository->getCountByYearAndCategory(
            new Year(2018),
            ModelsFactory::createMissingCategory()
        );

        $this->assertNull($foundQuestionCount);
    }
}
