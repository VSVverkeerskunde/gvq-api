<?php

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
        if ($text === '') {
            throw new \InvalidArgumentException('Text argument cannot be empty.');
        }
        $this->text = $text;
    }
}
