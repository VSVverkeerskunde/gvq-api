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
        if (count($answers) < 2 || count($answers) > 3) {
            throw new \InvalidArgumentException('Amount of answers must be 2 or 3.');
        }
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
