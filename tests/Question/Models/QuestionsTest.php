<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class QuestionsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_iterate_over_questions(): void
    {
        $question1 = ModelsFactory::createAccidentQuestion();
        $question2 = ModelsFactory::createGeneralQuestion();

        $expectedAnswers = [
            $question1,
            $question2,
        ];

        $questions = new Questions(...$expectedAnswers);

        $actualQuestions = [];
        foreach ($questions as $question) {
            $actualQuestions[] = $question;
        }

        $this->assertEquals($expectedAnswers, $actualQuestions);
    }
}
