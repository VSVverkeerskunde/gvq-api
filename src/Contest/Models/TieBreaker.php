<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class TieBreaker
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Year
     */
    private $year;

    /**
     * @var QuizChannel
     */
    private $channel;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var NotEmptyString
     */
    private $question;

    /**
     * @var PositiveNumber|null
     */
    private $answer;

    /**
     * TieBreaker constructor.
     * @param UuidInterface $id
     * @param Year $year
     * @param QuizChannel $channel
     * @param Language $language
     * @param NotEmptyString $question
     * @param null|PositiveNumber $answer
     */
    public function __construct(
        UuidInterface $id,
        Year $year,
        QuizChannel $channel,
        Language $language,
        NotEmptyString $question,
        ?PositiveNumber $answer
    ) {
        $this->id = $id;
        $this->year = $year;
        $this->channel = $channel;
        $this->language = $language;
        $this->question = $question;
        $this->answer = $answer;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Year
     */
    public function getYear(): Year
    {
        return $this->year;
    }

    /**
     * @return QuizChannel
     */
    public function getChannel(): QuizChannel
    {
        return $this->channel;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return NotEmptyString
     */
    public function getQuestion(): NotEmptyString
    {
        return $this->question;
    }

    /**
     * @return null|PositiveNumber
     */
    public function getAnswer(): ?PositiveNumber
    {
        return $this->answer;
    }
}
