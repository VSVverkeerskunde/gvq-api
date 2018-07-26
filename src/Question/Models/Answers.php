<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Answers implements Collection
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

        // The internal answer list is always sorted on index.
        // This avoids issues when the answers are joined with the question.
        usort(
            $this->answers,
            function (Answer $a1, Answer $a2) {
                return $a1->getIndex()->toNative() - $a2->getIndex()->toNative();
            }
        );
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

    /**
     * @return Answer
     */
    public function getCorrectAnswer(): Answer
    {
        foreach ($this->answers as $currentAnswer) {
            if ($currentAnswer->isCorrect()) {
                return $currentAnswer;
            }
        }

        throw new \RuntimeException('Did not find a correct answer!');
    }
}
