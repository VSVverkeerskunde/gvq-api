<?php

namespace VSV\GVQ_API\Question;

class Answer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var NotEmptyString
     */
    private $text;

    /**
     * @param int $id
     * @param NotEmptyString $text
     */
    public function __construct(
        int $id,
        NotEmptyString $text
    ) {
        $this->id = $id;
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text->__toString();
    }
}
