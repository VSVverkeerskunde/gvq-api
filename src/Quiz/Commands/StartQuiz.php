<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Commands;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;

class StartQuiz
{
    /**
     * @var QuizParticipant
     */
    private $participant;

    /**
     * @var QuizChannel
     */
    private $quizChannel;

    /**
     * @var Alias|null
     */
    private $companyAlias;

    /**
     * @var Alias|null
     */
    private $partnerAlias;

    /**
     * @var UuidInterface|null
     */
    private $teamId;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var string|null $firstQuestionId
     */
    private $firstQuestionId;

    /**
     * StartQuiz constructor.
     * @param QuizParticipant $participant
     * @param QuizChannel $quizChannel
     * @param null|Alias $companyAlias
     * @param null|Alias $partnerAlias
     * @param null|UuidInterface $teamId
     * @param Language $language
     */
    public function __construct(
        QuizParticipant $participant,
        QuizChannel $quizChannel,
        ?Alias $companyAlias,
        ?Alias $partnerAlias,
        ?UuidInterface $teamId,
        Language $language,
        ?string $firstQuestionId
    ) {
        $this->participant = $participant;
        $this->quizChannel = $quizChannel;
        $this->companyAlias = $companyAlias;
        $this->partnerAlias = $partnerAlias;
        $this->teamId = $teamId;
        $this->language = $language;
        $this->firstQuestionId = $firstQuestionId;
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
    public function getQuizChannel(): QuizChannel
    {
        return $this->quizChannel;
    }

    /**
     * @return null|Alias
     */
    public function getCompanyAlias(): ?Alias
    {
        return $this->companyAlias;
    }

    /**
     * @return null|Alias
     */
    public function getPartnerAlias(): ?Alias
    {
        return $this->partnerAlias;
    }

    /**
     * @return null|UuidInterface
     */
    public function getTeamId(): ?UuidInterface
    {
        return $this->teamId;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return string|null
     */
    public function getFirstQuestionId(): ?string
    {
        return $this->firstQuestionId;
    }
}
