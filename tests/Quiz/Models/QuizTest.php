<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class QuizTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidParametersProvider
     * @param UuidInterface $uuid
     * @param QuizChannel $channel
     * @param null|Company $company
     * @param null|Partner $partner
     * @throws \Exception
     */
    public function it_throws_on_creating_quiz_with_invalid_parameters(
        UuidInterface $uuid,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner
    ): void {
        $name = '';
        $object = '';
        if ($company !== null) {
            $name = $company->getName()->toNative();
            $object = 'company';
        } elseif ($partner !== null) {
            $name = $partner->getName()->toNative();
            $object = 'partner';
        }

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Quiz of channel '.$channel->toNative().' cannot contain '.$object.', '.$name.' given.'
        );

        ModelsFactory::createCustomQuiz(
            $uuid,
            $channel,
            $company,
            $partner
        );
    }

    /**
     * @return array[]
     */
    public function invalidParametersProvider(): array
    {
        return [
            [
                Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b'),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                ModelsFactory::createCompany(),
                null,
            ],
            [
                Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b'),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                null,
                ModelsFactory::createNBPartner(),
            ],
            [
                Uuid::fromString('26ff775c-cd4c-4aab-bd3c-3afa9baebc6a'),
                new QuizChannel(QuizChannel::PARTNER),
                ModelsFactory::createCompany(),
                null,
            ],
            [
                Uuid::fromString('d73f5383-19d5-47a2-8673-a123c89baf4b'),
                new QuizChannel(QuizChannel::COMPANY),
                null,
                ModelsFactory::createNBPartner(),
            ],
            [
                Uuid::fromString('be57176b-3f5f-479a-9906-91c54faccb33'),
                new QuizChannel(QuizChannel::CUP),
                ModelsFactory::createCompany(),
                null,
            ],
            [
                Uuid::fromString('be57176b-3f5f-479a-9906-91c54faccb33'),
                new QuizChannel(QuizChannel::CUP),
                null,
                ModelsFactory::createNBPartner(),
            ],
        ];
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_throws_on_creating_company_quiz_without_company(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Quiz of channel company needs company parameter, null given.'
        );

        ModelsFactory::createCustomQuiz(
            Uuid::fromString('d73f5383-19d5-47a2-8673-a123c89baf4b'),
            new QuizChannel(QuizChannel::COMPANY),
            null,
            null
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_throws_on_creating_partner_quiz_without_partner(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Quiz of channel partner needs partner parameter, null given.'
        );

        ModelsFactory::createCustomQuiz(
            Uuid::fromString('26ff775c-cd4c-4aab-bd3c-3afa9baebc6a'),
            new QuizChannel(QuizChannel::PARTNER),
            null,
            null
        );
    }
}
