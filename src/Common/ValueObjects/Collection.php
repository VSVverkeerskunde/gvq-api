<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

interface Collection extends \IteratorAggregate, \Countable
{
    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return array
     */
    public function toArray(): array;
}
