<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Teams implements Collection
{
    /**
     * @var Team[]
     */
    private $teams;

    /**
     * @param Team ...$teams
     */
    public function __construct(Team ...$teams)
    {
        $this->teams = $teams;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->teams);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->teams);
    }

    /**
     * @return Team[]
     */
    public function toArray(): array
    {
        return $this->teams;
    }
}
