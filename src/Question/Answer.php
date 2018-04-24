<?php

namespace VSV\GVQ_API\Question;

class Answer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $text;

    /**
     * @param int $id
     * @param string $text
     */
    public function __construct(
        int $id,
        string $text
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
        return $this->text;
    }
}
