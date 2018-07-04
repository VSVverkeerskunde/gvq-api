<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

class QuizChannel
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string[]
     */
    private $allowedValues = [
        'particulier',
        'bedrijf',
        'partner',
    ];

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!in_array($value, $this->allowedValues)) {
            throw new \InvalidArgumentException('Invalid value: '.$value.' for quiz channel.');
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
     * @param QuizChannel $quizChannel
     * @return bool
     */
    public function equals(QuizChannel $quizChannel): bool
    {
        return $this->toNative() === $quizChannel->toNative();
    }
}
