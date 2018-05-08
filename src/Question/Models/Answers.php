<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

class Answers implements \IteratorAggregate
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
    public function getIterator()
    {
        return new \ArrayIterator($this->answers);
    }
}
