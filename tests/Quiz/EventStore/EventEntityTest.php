<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventStore;

use PHPUnit\Framework\TestCase;

class EventEntityTest extends TestCase
{
    /**
     * @var EventEntity
     */
    private $eventEntity;

    protected function setUp(): void
    {
        $this->eventEntity = new EventEntity(
            'uuid',
            1,
            'payload',
            'metadata',
            '2020-11-11T11:12:33+00:00',
            'type'
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id()
    {
        $this->assertEquals(
            'uuid',
            $this->eventEntity->getUuid()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_playhead()
    {
        $this->assertEquals(
            1,
            $this->eventEntity->getPlayhead()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_payload()
    {
        $this->assertEquals(
            'payload',
            $this->eventEntity->getPayload()
        );
    }

    /**
     * @test
     */
    public function it_stores_metadata()
    {
        $this->assertEquals(
            'metadata',
            $this->eventEntity->getMetadata()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_recorded_on()
    {
        $this->assertEquals(
            '2020-11-11T11:12:33+00:00',
            $this->eventEntity->getRecordedOn()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_type()
    {
        $this->assertEquals(
            'type',
            $this->eventEntity->getType()
        );
    }
}
