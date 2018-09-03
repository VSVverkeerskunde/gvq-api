<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class UniqueParticipantRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var UniqueParticipantRepository
     */
    private $uniqueParticipantRepository;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        $this->uniqueParticipantRepository = new UniqueParticipantRedisRepository(
            $this->redis
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_add_a_unique_participant(): void
    {
        $quiz = ModelsFactory::createIndividualQuiz();
        $statisticsKey = new StatisticsKey(StatisticsKey::INDIVIDUAL_NL);

        $this->redis->expects($this->once())
            ->method('sAdd')
            ->with(
                'unique_participants_individual_nl',
                $quiz->getParticipant()->getEmail()->toNative()
            );

        $this->uniqueParticipantRepository->add(
            $statisticsKey,
            $quiz->getParticipant()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_add_a_unique_participant_for_a_partner(): void
    {
        $quiz = ModelsFactory::createPartnerQuiz();
        $statisticsKey = new StatisticsKey(StatisticsKey::PARTNER_NL);
        /** @var Partner $partner */
        $partner = $quiz->getPartner();

        $this->redis->expects($this->once())
            ->method('sAdd')
            ->with(
                'unique_participants_'.$partner->getId()->toString().'_'.$statisticsKey->getLanguage()->toNative(),
                $quiz->getParticipant()->getEmail()->toNative()
            );

        $this->uniqueParticipantRepository->addForPartner(
            $statisticsKey,
            $quiz->getParticipant(),
            $partner
        );
    }

    /**
     * @test
     */
    public function it_can_get_count_of_unique_participants(): void
    {
        $statisticsKey = new StatisticsKey(StatisticsKey::INDIVIDUAL_NL);

        $this->redis->expects($this->once())
            ->method('sCard')
            ->with('unique_participants_'.$statisticsKey->toNative())
            ->willReturn(0);

        $this->uniqueParticipantRepository->getCount($statisticsKey);
    }

    /**
     * @test
     */
    public function it_can_get_count_of_unique_participants_for_partner(): void
    {
        $statisticsKey = new StatisticsKey(StatisticsKey::PARTNER_NL);
        $partner = ModelsFactory::createDatsPartner();

        $this->redis->expects($this->once())
            ->method('sCard')
            ->with('unique_participants_'.$partner->getId()->toString().'_'.$statisticsKey->getLanguage()->toNative())
            ->willReturn(0);

        $this->uniqueParticipantRepository->getCountForPartner($statisticsKey, $partner);
    }
}
