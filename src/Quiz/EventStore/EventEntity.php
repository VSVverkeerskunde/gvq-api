<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventStore;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="event_store")
 */
class EventEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="play_head", type="integer", nullable=false)
     */
    private $playHead;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $payload;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $metadata;

    /**
     * @var string
     *
     * @ORM\Column(name="recorded_on", type="string",length=32, nullable=false)
     */
    private $recordedOn;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $type;

    /**
     * EventEntity constructor.
     * @param string $uuid
     * @param int $playHead
     * @param string $payload
     * @param string $metadata
     * @param string $recordedOn
     * @param string $type
     */
    public function __construct(
        string $uuid,
        int $playHead,
        string $payload,
        string $metadata,
        string $recordedOn,
        string $type
    ) {
        $this->uuid = $uuid;
        $this->playHead = $playHead;
        $this->payload = $payload;
        $this->metadata = $metadata;
        $this->recordedOn = $recordedOn;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getPlayHead(): int
    {
        return $this->playHead;
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getMetadata(): string
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getRecordedOn(): string
    {
        return $this->recordedOn;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
