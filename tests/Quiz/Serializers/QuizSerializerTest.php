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
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class QuizSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $quizAsJson;

    /**
     * @var Quiz
     */
    private $quiz;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $normalizers = [
            new QuizNormalizer(
                new CompanyNormalizer(
                    new TranslatedAliasNormalizer(),
                    new UserNormalizer()
                ),
                new PartnerNormalizer(),
                new QuestionNormalizer(
                    new CategoryNormalizer(),
                    new AnswerNormalizer()
                )
            ),
            new QuizDenormalizer(
                new CompanyDenormalizer(
                    new TranslatedAliasDenormalizer(),
                    new UserDenormalizer()
                ),
                new PartnerDenormalizer(),
                new QuestionDenormalizer(
                    new CategoryDenormalizer(),
                    new AnswerDenormalizer()
                )
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->quizAsJson = ModelsFactory::createJson('quiz_full');
        $this->quiz = ModelsFactory::createFullQuiz();
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->quiz,
            'json'
        );

        $this->assertEquals(
            $this->quizAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_quiz(): void
    {
        $actualQuiz = $this->serializer->deserialize(
            $this->quizAsJson,
            Quiz::class,
            'json'
        );

        $this->assertEquals(
            $this->quiz,
            $actualQuiz
        );
    }
}
