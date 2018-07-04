<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizType;
use VSV\GVQ_API\User\ValueObjects\Email;

class Quiz
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Email
     */
    private $participant;

    /**
     * @var QuizType
     */
    private $type;

    /**
     * @var QuizChannel
     */
    private $channel;

    /**
     * @var Language $language
     */
    private $language;

    /**
     * @var Year
     */
    private $year;

    /**
     * @var Questions
     */
    private $questions;

    /**
     * @param UuidInterface $id
     * @param Email $participant
     * @param QuizType $type
     * @param QuizChannel $channel
     * @param Language $language
     * @param Year $year
     * @param Questions $questions
     */
    public function __construct(
        UuidInterface $id,
        Email $participant,
        QuizType $type,
        QuizChannel $channel,
        Language $language,
        Year $year,
        Questions $questions
    ) {
        $this->id = $id;
        $this->participant = $participant;
        $this->type = $type;
        $this->channel = $channel;
        $this->language = $language;
        $this->year = $year;
        $this->questions = $questions;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Email
     */
    public function getParticipant(): Email
    {
        return $this->participant;
    }

    /**
     * @return QuizType
     */
    public function getType(): QuizType
    {
        return $this->type;
    }

    /**
     * @return QuizChannel
     */
    public function getChannel(): QuizChannel
    {
        return $this->channel;
    }

    /**
     * @return Year
     */
    public function getYear(): Year
    {
        return $this->year;
    }

    /**
     * @return Questions
     */
    public function getQuestions(): Questions
    {
        return $this->questions;
    }
}
