<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class TeamScores implements Collection
{
    /**
     * @var TeamScore[]
     */
    private $teamScores;

    /**
     * @param TeamScore ...$teamScores
     */
    public function __construct(TeamScore ...$teamScores)
    {
        $this->teamScores = $teamScores;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->teamScores);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->teamScores);
    }

    /**
     * @return TeamScore[]
     */
    public function toArray(): array
    {
        return $this->teamScores;
    }
}
