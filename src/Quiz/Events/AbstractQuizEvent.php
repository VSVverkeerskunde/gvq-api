<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;

abstract class AbstractQuizEvent
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @param UuidInterface $id
     */
    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
