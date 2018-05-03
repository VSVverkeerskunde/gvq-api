<?php

namespace VSV\GVQ_API\Question;

use Ramsey\Uuid\UuidInterface;

class Answer
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var NotEmptyString
     */
    private $text;

    /**
     * @param UuidInterface $id
     * @param NotEmptyString $text
     */
    public function __construct(
        UuidInterface $id,
        NotEmptyString $text
    ) {
        $this->id = $id;
        $this->text = $text;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
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
