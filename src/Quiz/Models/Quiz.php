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
use VSV\GVQ_API\Team\Models\Team;

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
     * @var null|Team
     */
    private $team;

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
     * @param QuizChannel $channel
     * @param null|Company $company
     * @param null|Partner $partner
     * @param null|Team $team
     * @param Language $language
     * @param Year $year
     * @param AllowedDelay $allowedDelay
     * @param Questions $questions
     */
    public function __construct(
        UuidInterface $id,
        QuizParticipant $participant,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team,
        Language $language,
        Year $year,
        AllowedDelay $allowedDelay,
        Questions $questions
    ) {
        $this->guardChannel($channel, $company, $partner, $team);

        $this->id = $id;
        $this->participant = $participant;
        $this->channel = $channel;
        $this->company = $company;
        $this->partner = $partner;
        $this->team = $team;
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
     * @return null|Team
     */
    public function getTeam(): ?Team
    {
        return $this->team;
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
     * @param null|Company $company
     * @param null|Partner $partner
     * @param null|Team $team
     */
    private function guardChannel(QuizChannel $channel, ?Company $company, ?Partner $partner, ?Team $team): void
    {
        switch ($channel->toNative()) {
            case QuizChannel::INDIVIDUAL:
            case QuizChannel::LEAGUE:
                $this->checkForDisallowedTeam($channel, $team);
                $this->checkForDisallowedCompany($channel, $company);
                $this->checkForDisallowedPartner($channel, $partner);
                break;
            case QuizChannel::CUP:
                if ($team === null) {
                    throw new \InvalidArgumentException('Quiz of channel cup needs team parameter, null given.');
                }
                $this->checkForDisallowedCompany($channel, $company);
                $this->checkForDisallowedPartner($channel, $partner);
                break;
            case QuizChannel::PARTNER:
                $this->checkForDisallowedTeam($channel, $team);
                $this->checkForDisallowedCompany($channel, $company);
                if ($partner === null) {
                    throw new \InvalidArgumentException('Quiz of channel partner needs partner parameter, null given.');
                }
                break;
            case QuizChannel::COMPANY:
                $this->checkForDisallowedTeam($channel, $team);
                $this->checkForDisallowedPartner($channel, $partner);
                if ($company === null) {
                    throw new \InvalidArgumentException('Quiz of channel company needs company parameter, null given.');
                }
                break;
        }
    }

    /**
     * @param QuizChannel $channel
     * @param null|Company $company
     */
    private function checkForDisallowedCompany(QuizChannel $channel, ?Company $company): void
    {
        if ($company !== null) {
            throw new \InvalidArgumentException(
                'Quiz of channel '.
                $channel->toNative().
                ' cannot contain company, '.
                $company->getName()->toNative().
                ' given.'
            );
        }
    }

    /**
     * @param QuizChannel $channel
     * @param null|Partner $partner
     */
    private function checkForDisallowedPartner(QuizChannel $channel, ?Partner $partner): void
    {
        if ($partner !== null) {
            throw new \InvalidArgumentException(
                'Quiz of channel '.
                $channel->toNative().
                ' cannot contain partner, '.
                $partner->getName()->toNative().
                ' given.'
            );
        }
    }

    private function checkForDisallowedTeam(QuizChannel $channel, ?Team $team): void
    {
        if ($team !== null) {
            throw new \InvalidArgumentException(
                'Quiz of channel '.
                $channel->toNative().
                ' cannot contain team, '.
                $team->getName()->toNative().
                ' given.'
            );
        }
    }
}
