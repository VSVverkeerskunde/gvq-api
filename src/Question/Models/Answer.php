<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

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
     * @var bool
     */
    private $correct;

    /**
     * @param UuidInterface $id
     * @param NotEmptyString $text
     * @param bool $correct
     */
    public function __construct(
        UuidInterface $id,
        NotEmptyString $text,
        bool $correct
    ) {
        $this->id = $id;
        $this->text = $text;
        $this->correct = $correct;
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

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }
}
