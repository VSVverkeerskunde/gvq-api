<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Contest\ValueObjects\Address;
use VSV\GVQ_API\Contest\ValueObjects\ContestParticipant;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class ContestParticipation
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
     * @var Language
     */
    private $language;

    /**
     * @var QuizChannel
     */
    private $channel;

    /**
     * @var ContestParticipant
     */
    private $contestParticipant;

    /**
     * @var Address|null
     */
    private $address;

    /**
     * @var PositiveNumber
     */
    private $answer1;

    /**
     * @var PositiveNumber
     */
    private $answer2;

    /**
     * @var bool
     */
    private $gdpr1;

    /**
     * @var bool
     */
    private $gdpr2;

    /**
     * @var bool|null
     */
    private $associationMember;

    /**
     * @param UuidInterface $id
     * @param Year $year
     * @param Language $language
     * @param QuizChannel $channel
     * @param ContestParticipant $contestParticipant
     * @param Address $address
     * @param PositiveNumber $answer1
     * @param PositiveNumber $answer2
     * @param bool $gdpr1
     * @param bool $gdpr2
     * @param bool $associationMember
     */
    public function __construct(
        UuidInterface $id,
        Year $year,
        Language $language,
        QuizChannel $channel,
        ContestParticipant $contestParticipant,
        ?Address $address,
        PositiveNumber $answer1,
        PositiveNumber $answer2,
        bool $gdpr1,
        bool $gdpr2,
        ?bool $associationMember = false
    ) {
        $this->guardChannel($channel);
        $this->guardGdpr($gdpr1);

        $this->id = $id;
        $this->year = $year;
        $this->language = $language;
        $this->channel = $channel;
        $this->contestParticipant = $contestParticipant;
        $this->address = $address;
        $this->answer1 = $answer1;
        $this->answer2 = $answer2;
        $this->gdpr1 = $gdpr1;
        $this->gdpr2 = $gdpr2;

        $this->associationMember = $associationMember;
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
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return QuizChannel
     */
    public function getChannel(): QuizChannel
    {
        return $this->channel;
    }

    /**
     * @return ContestParticipant
     */
    public function getContestParticipant(): ContestParticipant
    {
        return $this->contestParticipant;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @return PositiveNumber
     */
    public function getAnswer1(): PositiveNumber
    {
        return $this->answer1;
    }

    /**
     * @return PositiveNumber
     */
    public function getAnswer2(): PositiveNumber
    {
        return $this->answer2;
    }

    /**
     * @return bool
     */
    public function isGdpr1(): bool
    {
        return $this->gdpr1;
    }

    /**
     * @return bool
     */
    public function isGdpr2(): bool
    {
        return $this->gdpr2;
    }

    /**
     * @return bool
     */
    public function isAssociationMember(): bool
    {
        if (is_null($this->associationMember)) {
            return false;
        }

        return $this->associationMember;
    }

    /**
     * @param QuizChannel $quizChannel
     */
    private function guardChannel(QuizChannel $quizChannel): void
    {
        if (!$quizChannel->equals(new QuizChannel(QuizChannel::INDIVIDUAL)) &&
            !$quizChannel->equals(new QuizChannel(QuizChannel::LEAGUE))) {
            throw new \InvalidArgumentException(
                'Invalid value "'.$quizChannel->toNative().'" for quiz channel.'.
                ' Allowed values are '.QuizChannel::INDIVIDUAL.' and '.QuizChannel::LEAGUE.'.'
            );
        }
    }

    /**
     * @param bool $gdpr
     */
    private function guardGdpr(bool $gdpr): void
    {
        if ($gdpr === false) {
            throw new \InvalidArgumentException('GDPR1 should be accepted.');
        }
    }
}
