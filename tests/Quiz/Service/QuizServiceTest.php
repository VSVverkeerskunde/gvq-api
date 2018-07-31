<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Factory\QuestionsGenerator;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Repositories\QuizCompositionRepository;
use VSV\GVQ_API\Quiz\ValueObjects\AllowedDelay;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\User\ValueObjects\Email;

class QuizServiceTest extends TestCase
{
    /**
     * @var QuizService
     */
    private $quizService;
    /**
     * @var QuestionRepository|MockObject
     */
    private $questionRepository;

    /**
     * @var CategoryRepository|MockObject
     */
    private $categoryRepository;

    /**
     * @var QuizCompositionRepository|MockObject
     */
    private $quizCompositionRepository;

    /**
     * @var UuidFactoryInterface|MockObject
     */
    private $uuidFactoryInterface;

    protected function setUp(): void
    {
        /** @var QuestionRepository|MockObject $questionRepository */
        $questionRepository = $this->createMock(QuestionRepository::class);
        $this->questionRepository = $questionRepository;

        /** @var CategoryRepository|MockObject $categoryRepository */
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $this->categoryRepository = $categoryRepository;

        /** @var QuizCompositionRepository|MockObject $quizCompositionRepository */
        $quizCompositionRepository = $this->createMock(QuizCompositionRepository::class);
        $this->quizCompositionRepository = $quizCompositionRepository;

        /** @var UuidFactoryInterface|MockObject $uuidFactoryInterface */
        $uuidFactoryInterface = $this->createMock(UuidFactoryInterface::class);
        $this->uuidFactoryInterface = $uuidFactoryInterface;

        $this->quizService = new QuizService(
            $this->questionRepository,
            $this->categoryRepository,
            $this->quizCompositionRepository,
            $this->uuidFactoryInterface,
            new Year(2018),
            new AllowedDelay(40)
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_generate_a_random_quiz_with_correct_distribution(): void
    {
        $this->uuidFactoryInterface
            ->expects($this->exactly(2))
            ->method('uuid4')
            ->willReturn(Uuid::fromString('31ea2c50-1eb5-4088-9a72-1c705ec44378'));

        $categories = ModelsFactory::createAllCategories();
        $this->categoryRepository
            ->expects($this->exactly(2))
            ->method('getAll')
            ->willReturn($categories);

        $language = new Language('nl');
        $year = new Year(2018);

        $this->quizCompositionRepository
            ->expects($this->exactly(16))
            ->method('getCountByYearAndCategory')
            ->withConsecutive($year, $this->onConsecutiveCalls($categories, $categories))
            ->willReturnOnConsecutiveCalls(1, 2, 3, 2, 2, 2, 2, 2, 1, 2, 3, 2, 2, 2, 2, 2);

        $this->questionRepository
            ->expects($this->exactly(16))
            ->method('getByYearLanguageAndCategory')
            ->withConsecutive($year, $language, $this->onConsecutiveCalls($categories, $categories))
            ->willReturnOnConsecutiveCalls(
                QuestionsGenerator::generateForCategory($categories->toArray()[0]),
                QuestionsGenerator::generateForCategory($categories->toArray()[1]),
                QuestionsGenerator::generateForCategory($categories->toArray()[2]),
                QuestionsGenerator::generateForCategory($categories->toArray()[3]),
                QuestionsGenerator::generateForCategory($categories->toArray()[4]),
                QuestionsGenerator::generateForCategory($categories->toArray()[5]),
                QuestionsGenerator::generateForCategory($categories->toArray()[6]),
                QuestionsGenerator::generateForCategory($categories->toArray()[7]),
                QuestionsGenerator::generateForCategory($categories->toArray()[0]),
                QuestionsGenerator::generateForCategory($categories->toArray()[1]),
                QuestionsGenerator::generateForCategory($categories->toArray()[2]),
                QuestionsGenerator::generateForCategory($categories->toArray()[3]),
                QuestionsGenerator::generateForCategory($categories->toArray()[4]),
                QuestionsGenerator::generateForCategory($categories->toArray()[5]),
                QuestionsGenerator::generateForCategory($categories->toArray()[6]),
                QuestionsGenerator::generateForCategory($categories->toArray()[7])
            );

        $participant = new QuizParticipant(new Email('par@ticipa.nt'));
        $channel = new QuizChannel('individual');

        $quiz = $this->quizService->generateQuiz(
            $participant,
            $channel,
            null,
            null,
            null,
            $language
        );

        $quiz2 = $this->quizService->generateQuiz(
            $participant,
            $channel,
            null,
            null,
            null,
            $language
        );

        $this->assertNotEquals(
            $quiz->getQuestions(),
            $quiz2->getQuestions()
        );

        $this->assertEquals(
            16,
            $quiz->getQuestions()->count()
        );

        $numberPerCategory = [
            'Algemene verkeersregels' => 0,
            'Kwetsbare weggebruikers' => 0,
            'Verkeerstekens' => 0,
            'Voorrang' => 0,
            'EHBO/Ongeval/Verzekering' => 0,
            'Voertuig/Technieks' => 0,
            'Openbaar vervoer/Milieu' => 0,
            'Verkeersveiligheid' => 0,
        ];

        $expectedNumberPerCategory = [
            'Algemene verkeersregels' => 1,
            'Kwetsbare weggebruikers' => 2,
            'Verkeerstekens' => 3,
            'Voorrang' => 2,
            'EHBO/Ongeval/Verzekering' => 2,
            'Voertuig/Technieks' => 2,
            'Openbaar vervoer/Milieu' => 2,
            'Verkeersveiligheid' => 2,
        ];

        foreach ($quiz->getQuestions()->toArray() as $question) {
            $numberPerCategory[$question->getCategory()->getName()->toNative()] += 1;
        }

        $this->assertEquals(
            $expectedNumberPerCategory,
            $numberPerCategory
        );
    }
}
