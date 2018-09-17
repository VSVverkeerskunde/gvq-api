<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Serializers\CompanyDenormalizer;
use VSV\GVQ_API\Company\Serializers\CompanyNormalizer;
use VSV\GVQ_API\Company\Serializers\TranslatedAliasDenormalizer;
use VSV\GVQ_API\Company\Serializers\TranslatedAliasNormalizer;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Partner\Serializers\PartnerDenormalizer;
use VSV\GVQ_API\Partner\Serializers\PartnerNormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryDenormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Team\Models\Team;
use VSV\GVQ_API\Team\Serializers\TeamDenormalizer;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class QuizSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

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
                new TeamNormalizer(),
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
                new TeamDenormalizer(),
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
    }

    /**
     * @test
     * @dataProvider parametersProvider
     * @param UuidInterface $uuid
     * @param QuizChannel $channel
     * @param null|Company $company
     * @param null|Partner $partner
     * @param null|Team $team
     * @throws \Exception
     */
    public function it_can_serialize_to_json(
        UuidInterface $uuid,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team
    ): void {
        $quizAsJson = ModelsFactory::createJson('quiz_'.$channel->toNative());
        $quiz = ModelsFactory::createCustomQuiz(
            $uuid,
            $channel,
            $company,
            $partner,
            $team
        );

        $actualJson = $this->serializer->serialize(
            $quiz,
            'json'
        );

        $this->assertEquals(
            $quizAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @dataProvider parametersProvider
     * @param UuidInterface $uuid
     * @param QuizChannel $channel
     * @param null|Company $company
     * @param null|Partner $partner
     * @param null|Team $team
     * @throws \Exception
     */
    public function it_can_deserialize_to_quiz(
        UuidInterface $uuid,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team
    ): void {
        $quizAsJson = ModelsFactory::createJson('quiz_'.$channel->toNative());
        $quiz = ModelsFactory::createCustomQuiz(
            $uuid,
            $channel,
            $company,
            $partner,
            $team
        );

        $actualQuiz = $this->serializer->deserialize(
            $quizAsJson,
            Quiz::class,
            'json'
        );

        $this->assertEquals(
            $quiz,
            $actualQuiz
        );
    }

    /**
     * @return array[]
     */
    public function parametersProvider(): array
    {
        return [
            [
                Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b'),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                null,
                null,
                null,
            ],
            [
                Uuid::fromString('be57176b-3f5f-479a-9906-91c54faccb33'),
                new QuizChannel(QuizChannel::CUP),
                null,
                null,
                ModelsFactory::createAntwerpTeam(),
            ],
            [
                Uuid::fromString('26ff775c-cd4c-4aab-bd3c-3afa9baebc6a'),
                new QuizChannel(QuizChannel::PARTNER),
                null,
                ModelsFactory::createNBPartner(),
                null,
            ],
            [
                Uuid::fromString('d73f5383-19d5-47a2-8673-a123c89baf4b'),
                new QuizChannel(QuizChannel::COMPANY),
                ModelsFactory::createCompany(),
                null,
                null,
            ],
        ];
    }
}
