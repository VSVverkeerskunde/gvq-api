<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Models\Quiz;

class StatisticsKeyTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider quizProvider
     * @param Quiz $quiz
     * @param StatisticsKey $statisticsKey
     */
    public function it_can_be_created_from_a_quiz(
        Quiz $quiz,
        StatisticsKey $statisticsKey
    ) {
        $this->assertEquals(
            $statisticsKey,
            StatisticsKey::createFromQuiz($quiz)
        );
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function quizProvider(): array
    {
        return [
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::INDIVIDUAL),
                    null,
                    null,
                    null,
                    new Language(Language::NL)
                ),
                new StatisticsKey(StatisticsKey::INDIVIDUAL_NL),
            ],
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::INDIVIDUAL),
                    null,
                    null,
                    null,
                    new Language(Language::FR)
                ),
                new StatisticsKey(StatisticsKey::INDIVIDUAL_FR),
            ],
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::COMPANY),
                    ModelsFactory::createCompany(),
                    null,
                    null,
                    new Language(Language::NL)
                ),
                new StatisticsKey(StatisticsKey::COMPANY_NL),
            ],
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::COMPANY),
                    ModelsFactory::createCompany(),
                    null,
                    null,
                    new Language(Language::FR)
                ),
                new StatisticsKey(StatisticsKey::COMPANY_FR),
            ],
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::PARTNER),
                    null,
                    ModelsFactory::createDatsPartner(),
                    null,
                    new Language(Language::NL)
                ),
                new StatisticsKey(StatisticsKey::PARTNER_NL),
            ],
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::PARTNER),
                    null,
                    ModelsFactory::createDatsPartner(),
                    null,
                    new Language(Language::FR)
                ),
                new StatisticsKey(StatisticsKey::PARTNER_FR),
            ],
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::CUP),
                    null,
                    null,
                    ModelsFactory::createTeam(),
                    new Language(Language::NL)
                ),
                new StatisticsKey(StatisticsKey::CUP_NL),
            ],
            [
                ModelsFactory::createCustomQuiz(
                    Uuid::uuid4(),
                    new QuizChannel(QuizChannel::CUP),
                    null,
                    null,
                    ModelsFactory::createTeam(),
                    new Language(Language::FR)
                ),
                new StatisticsKey(StatisticsKey::CUP_FR),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider statisticsKeyProvider
     * @param StatisticsKey $statisticsKey
     * @param Language $language
     */
    public function it_can_get_the_language_of_a_key(
        StatisticsKey $statisticsKey,
        Language $language
    ): void {
        $this->assertEquals(
            $language,
            $statisticsKey->getLanguage()
        );
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function statisticsKeyProvider(): array
    {
        return [
            [
                new StatisticsKey(StatisticsKey::INDIVIDUAL_NL),
                new Language(Language::NL),
            ],
            [
                new StatisticsKey(StatisticsKey::INDIVIDUAL_FR),
                new Language(Language::FR),
            ],
            [
                new StatisticsKey(StatisticsKey::COMPANY_NL),
                new Language(Language::NL),
            ],
            [
                new StatisticsKey(StatisticsKey::COMPANY_FR),
                new Language(Language::FR),
            ],
            [
                new StatisticsKey(StatisticsKey::PARTNER_NL),
                new Language(Language::NL),
            ],
            [
                new StatisticsKey(StatisticsKey::PARTNER_FR),
                new Language(Language::FR),
            ],
            [
                new StatisticsKey(StatisticsKey::CUP_NL),
                new Language(Language::NL),
            ],
            [
                new StatisticsKey(StatisticsKey::CUP_FR),
                new Language(Language::FR),
            ],
        ];
    }
}
