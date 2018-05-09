<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

class Answers implements \IteratorAggregate, \Countable
{
    /**
     * @var Answer[]
     */
    private $answers;

    /**
     * @param Answer ...$answers
     */
    public function __construct(Answer ...$answers)
    {
        $this->answers = $answers;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->answers);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->answers);
    }

    /**
     * @return Answer[]
     */
    public function toArray(): array
    {
        return $this->answers;
    }
}
