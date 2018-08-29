<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
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
        $quiz = ModelsFactory::createPartnerQuiz();
        $statisticsKey = new StatisticsKey('partner_nl');

        $this->redis->expects($this->exactly(2))
            ->method('sAdd')
            ->withConsecutive(
                [
                    'unique_participants_partner_nl',
                    $quiz->getParticipant()->getEmail()->toNative(),
                ],
                [
                    'unique_participants_dats24_nl',
                    $quiz->getParticipant()->getEmail()->toNative(),
                ]
            );

        $this->uniqueParticipantRepository->add(
            $statisticsKey,
            $quiz->getParticipant(),
            $quiz->getPartner()
        );
    }

    /**
     * @test
     */
    public function it_can_get_count_of_unique_participants(): void
    {
        $statisticsKey = new StatisticsKey('individual_nl');

        $this->redis->expects($this->once())
            ->method('sCard')
            ->with('unique_participants_'.$statisticsKey->toNative())
            ->willReturn(0);

        $this->uniqueParticipantRepository->getCount($statisticsKey);
    }
}
