<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Serializers\CompanyDenormalizer;
use VSV\GVQ_API\Company\Serializers\CompanyNormalizer;
use VSV\GVQ_API\Company\Serializers\TranslatedAliasDenormalizer;
use VSV\GVQ_API\Company\Serializers\TranslatedAliasNormalizer;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Serializers\PartnerDenormalizer;
use VSV\GVQ_API\Partner\Serializers\PartnerNormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryDenormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Team\Serializers\TeamDenormalizer;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class QuizStartedSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $normalizers = [
            new QuizStartedNormalizer(
                $this->createQuizNormalizer()
            ),
            new QuizStartedDenormalizer(
                $this->createQuizDenormalizer()
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_to_json(): void
    {
        $quizStartedAsJson = ModelsFactory::createJson('quiz_started');
        $quizStarted = ModelsFactory::createQuizStarted();

        $actualJson = $this->serializer->serialize(
            $quizStarted,
            'json'
        );

        $this->assertEquals(
            $quizStartedAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_quiz_started(): void
    {
        $quizStartedAsJson = ModelsFactory::createJson('quiz_started');
        $quizStarted = ModelsFactory::createQuizStarted();

        $actualQuizStarted = $this->serializer->deserialize(
            $quizStartedAsJson,
            QuizStarted::class,
            'json'
        );

        $this->assertEquals(
            $quizStarted,
            $actualQuizStarted
        );
    }

    /**
     * @return QuizNormalizer
     */
    private function createQuizNormalizer(): QuizNormalizer
    {
        return new QuizNormalizer(
            new CompanyNormalizer(
                new TranslatedAliasNormalizer(),
                new UserNormalizer()
            ),
            new PartnerNormalizer(),
            new TeamNormalizer(),
            new QuestionNormalizer(
                new CategoryNormalizer(),
                new AnswerNormalizer()
            )
        );
    }

    /**
     * @return QuizDenormalizer
     */
    private function createQuizDenormalizer(): QuizDenormalizer
    {
        return new QuizDenormalizer(
            new CompanyDenormalizer(
                new TranslatedAliasDenormalizer(),
                new UserDenormalizer()
            ),
            new PartnerDenormalizer(),
            new TeamDenormalizer(),
            new QuestionDenormalizer(
                new CategoryDenormalizer(),
                new AnswerDenormalizer()
            )
        );
    }
}
