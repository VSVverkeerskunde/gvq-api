<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class ContestParticipations implements Collection
{
    /**
     * @var ContestParticipation[]
     */
    private $contestParticipations;

    /**
     * @param ContestParticipation ...$contestParticipations
     */
    public function __construct(ContestParticipation ...$contestParticipations)
    {
        $this->contestParticipations = $contestParticipations;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->contestParticipations);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->contestParticipations);
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return $this->contestParticipations;
    }
}
