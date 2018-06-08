<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;

class Answer
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var PositiveNumber
     */
    private $index;

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
     * @param PositiveNumber $index
     * @param NotEmptyString $text
     * @param bool $correct
     */
    public function __construct(
        UuidInterface $id,
        PositiveNumber $index,
        NotEmptyString $text,
        bool $correct
    ) {
        $this->id = $id;
        $this->index = $index;
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
     * @return PositiveNumber
     */
    public function getIndex(): PositiveNumber
    {
        return $this->index;
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
