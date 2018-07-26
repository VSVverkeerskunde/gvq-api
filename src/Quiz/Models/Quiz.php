<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\AllowedDelay;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Quiz\ValueObjects\QuizType;

class Quiz
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var QuizParticipant
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
     * @var null|Company
     */
    private $company;

    /**
     * @var null|Partner
     */
    private $partner;

    /**
     * @var Language $language
     */
    private $language;

    /**
     * @var Year
     */
    private $year;

    /**
     * @var AllowedDelay
     */
    private $allowedDelay;

    /**
     * @var Questions
     */
    private $questions;

    /**
     * @param UuidInterface $id
     * @param QuizParticipant $participant
     * @param QuizType $type
     * @param QuizChannel $channel
     * @param null|Company $company
     * @param null|Partner $partner
     * @param Language $language
     * @param Year $year
     * @param AllowedDelay $allowedDelay
     * @param Questions $questions
     */
    public function __construct(
        UuidInterface $id,
        QuizParticipant $participant,
        QuizType $type,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        Language $language,
        Year $year,
        AllowedDelay $allowedDelay,
        Questions $questions
    ) {
        $this->guardChannel($channel, $type);

        $this->id = $id;
        $this->participant = $participant;
        $this->type = $type;
        $this->channel = $channel;
        $this->company = $company;
        $this->partner = $partner;
        $this->language = $language;
        $this->year = $year;
        $this->questions = $questions;
        $this->allowedDelay = $allowedDelay;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return QuizParticipant
     */
    public function getParticipant(): QuizParticipant
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
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @return Partner|null
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return Year
     */
    public function getYear(): Year
    {
        return $this->year;
    }

    /**
     * @return AllowedDelay
     */
    public function getAllowedDelay(): AllowedDelay
    {
        return $this->allowedDelay;
    }

    /**
     * @return Questions
     */
    public function getQuestions(): Questions
    {
        return $this->questions;
    }

    /**
     * @param QuizChannel $channel
     * @param QuizType $type
     */
    private function guardChannel(QuizChannel $channel, QuizType $type): void
    {
        if ($channel->toNative() === QuizChannel::PARTNER && $type->toNative() !== QuizType::QUIZ) {
            throw new \InvalidArgumentException('Quiz of channel partner can not be of type '.$type->toNative());
        }
    }
}
