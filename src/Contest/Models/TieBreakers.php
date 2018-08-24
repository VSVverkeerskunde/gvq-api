<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class TieBreakers implements Collection
{
    /**
     * @var TieBreaker[]
     */
    private $tieBreakers;

    /**
     * @param TieBreaker ...$tieBreakers
     */
    public function __construct(...$tieBreakers)
    {
        $this->tieBreakers = $tieBreakers;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->tieBreakers);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->tieBreakers);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->tieBreakers;
    }
}
