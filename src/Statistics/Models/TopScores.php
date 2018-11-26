<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class TopScores implements Collection
{
    /**
     * @var TopScore[]
     */
    private $topScores;

    /**
     * TopScores constructor.
     * @param TopScore ...$topScores
     */
    public function __construct(TopScore ...$topScores)
    {
        $this->topScores = $topScores;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->topScores);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->topScores);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->topScores;
    }
}
