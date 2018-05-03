<?php declare(strict_types=1);

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
     * @return NotEmptyString
     */
    public function getText(): NotEmptyString
    {
        return $this->text;
    }
}
