<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Team\Models\Team;

class QuizTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidParametersProvider
     * @param UuidInterface $uuid
     * @param QuizChannel $channel
     * @param null|Company $company
     * @param null|Partner $partner
     * @param null|Team $team
     * @throws \Exception
     */
    public function it_throws_on_creating_quiz_with_invalid_parameters(
        UuidInterface $uuid,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team
    ): void {
        $name = '';
        $object = '';
        if ($company !== null) {
            $name = $company->getName()->toNative();
            $object = 'company';
        } elseif ($partner !== null) {
            $name = $partner->getName()->toNative();
            $object = 'partner';
        } elseif ($team !== null) {
            $name = $team->getName()->toNative();
            $object = 'team';
        }

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Quiz of channel '.$channel->toNative().' cannot contain '.$object.', '.$name.' given.'
        );

        ModelsFactory::createCustomQuiz(
            $uuid,
            $channel,
            $company,
            $partner,
            $team
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
                null,
            ],
            [
                Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b'),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                null,
                ModelsFactory::createNBPartner(),
                null,
            ],
            [
                Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b'),
                new QuizChannel(QuizChannel::INDIVIDUAL),
                null,
                null,
                ModelsFactory::createTeam(),
            ],
            [
                Uuid::fromString('26ff775c-cd4c-4aab-bd3c-3afa9baebc6a'),
                new QuizChannel(QuizChannel::PARTNER),
                ModelsFactory::createCompany(),
                null,
                null,
            ],
            [
                Uuid::fromString('26ff775c-cd4c-4aab-bd3c-3afa9baebc6a'),
                new QuizChannel(QuizChannel::PARTNER),
                null,
                null,
                ModelsFactory::createTeam(),
            ],
            [
                Uuid::fromString('d73f5383-19d5-47a2-8673-a123c89baf4b'),
                new QuizChannel(QuizChannel::COMPANY),
                null,
                ModelsFactory::createNBPartner(),
                null,
            ],
            [
                Uuid::fromString('d73f5383-19d5-47a2-8673-a123c89baf4b'),
                new QuizChannel(QuizChannel::COMPANY),
                null,
                null,
                ModelsFactory::createTeam(),
            ],
            [
                Uuid::fromString('be57176b-3f5f-479a-9906-91c54faccb33'),
                new QuizChannel(QuizChannel::CUP),
                ModelsFactory::createCompany(),
                null,
                ModelsFactory::createTeam(),
            ],
            [
                Uuid::fromString('be57176b-3f5f-479a-9906-91c54faccb33'),
                new QuizChannel(QuizChannel::CUP),
                null,
                ModelsFactory::createNBPartner(),
                ModelsFactory::createTeam(),
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
            null,
            null
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_throws_on_creating_cup_quiz_without_team(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Quiz of channel cup needs team parameter, null given.'
        );

        ModelsFactory::createCustomQuiz(
            Uuid::fromString('be57176b-3f5f-479a-9906-91c54faccb33'),
            new QuizChannel(QuizChannel::CUP),
            null,
            null,
            null
        );
    }
}
