<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question;

class NotEmptyString
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $text = trim($text);
        if ($text === '') {
            throw new \InvalidArgumentException('Text argument cannot be empty.');
        }
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->text;
    }

    /**
     * @param NotEmptyString $notEmptyString
     * @return bool
     */
    public function equals(NotEmptyString $notEmptyString): bool
    {
        return $this->toNative() === $notEmptyString->toNative();
    }
}
