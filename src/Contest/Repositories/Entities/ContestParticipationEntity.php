<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\Entities\Entity;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="contest_participation",
 *     indexes={
 *         @ORM\Index(name="email_index", columns={"participant_email"})
 *     }
 * )
 */
class ContestParticipationEntity extends Entity
{
    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $channel;

    /**
     * @var ContestParticipantEmbeddable
     *
     * @ORM\Embedded(
     *     class="VSV\GVQ_API\Contest\Repositories\Entities\ContestParticipantEmbeddable", columnPrefix="participant_")
     */
    private $contestParticipant;

    /**
     * @var AddressEmbeddable
     *
     * @ORM\Embedded(class="VSV\GVQ_API\Contest\Repositories\Entities\AddressEmbeddable")
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $answer1;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $answer2;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $gdpr1;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $gdpr2;

    /**
     * @param string $id
     * @param int $year
     * @param string $language
     * @param string $channel
     * @param ContestParticipantEmbeddable $contestParticipant
     * @param AddressEmbeddable $address
     * @param int $answer1
     * @param int $answer2
     * @param bool $gdpr1
     * @param bool $gdpr2
     */
    private function __construct(
        string $id,
        int $year,
        string $language,
        string $channel,
        ContestParticipantEmbeddable $contestParticipant,
        AddressEmbeddable $address,
        int $answer1,
        int $answer2,
        bool $gdpr1,
        bool $gdpr2
    ) {
        parent::__construct($id);

        $this->year = $year;
        $this->language = $language;
        $this->channel = $channel;
        $this->contestParticipant = $contestParticipant;
        $this->address = $address;
        $this->answer1 = $answer1;
        $this->answer2 = $answer2;
        $this->gdpr1 = $gdpr1;
        $this->gdpr2 = $gdpr2;
    }

    /**
     * @param ContestParticipation $contestParticipation
     * @return ContestParticipationEntity
     */
    public static function fromContestParticipation(
        ContestParticipation $contestParticipation
    ): ContestParticipationEntity {
        return new ContestParticipationEntity(
            $contestParticipation->getId()->toString(),
            $contestParticipation->getYear()->toNative(),
            $contestParticipation->getLanguage()->toNative(),
            $contestParticipation->getChannel()->toNative(),
            ContestParticipantEmbeddable::fromContestParticipation(
                $contestParticipation->getContestParticipant()
            ),
            AddressEmbeddable::fromAddress(
                $contestParticipation->getAddress()
            ),
            $contestParticipation->getAnswer1()->toNative(),
            $contestParticipation->getAnswer2()->toNative(),
            $contestParticipation->isGdpr1(),
            $contestParticipation->isGdpr2()
        );
    }

    /**
     * @return ContestParticipation
     */
    public function toContestParticipation(): ContestParticipation
    {
        return new ContestParticipation(
            Uuid::fromString($this->getId()),
            new Year($this->year),
            new Language($this->language),
            new QuizChannel($this->channel),
            $this->contestParticipant->toContestParticipant(),
            $this->address->toAddress(),
            new PositiveNumber($this->answer1),
            new PositiveNumber($this->answer2),
            $this->gdpr1,
            $this->gdpr2
        );
    }
}
