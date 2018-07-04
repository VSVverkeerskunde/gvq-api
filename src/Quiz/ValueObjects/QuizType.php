<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

class QuizType
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string[]
     */
    private $allowedValues = [
        'quiz',
        'cup',
    ];

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!in_array($value, $this->allowedValues)) {
            throw new \InvalidArgumentException('Invalid value: '.$value.' for quiz type.');
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->value;
    }

    /**
     * @param QuizType $quizType
     * @return bool
     */
    public function equals(QuizType $quizType): bool
    {
        return $this->toNative() === $quizType->toNative();
    }
}
