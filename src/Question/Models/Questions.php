<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

class Questions implements \IteratorAggregate, \Countable
{
    /**
     * @var Question[]
     */
    private $questions;

    /**
     * Questions constructor.
     * @param Question ...$questions
     */
    public function __construct(Question ...$questions)
    {
        $this->questions = $questions;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->questions);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->questions);
    }

    /**
     * @return Question[]
     */
    public function toArray(): array
    {
        return $this->questions;
    }
}
