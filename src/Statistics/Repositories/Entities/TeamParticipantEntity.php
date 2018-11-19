<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use VSV\GVQ_API\Statistics\Models\TeamParticipant;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="team_participant"
 * )
 */
class TeamParticipantEntity
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    private $teamId;

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    private $email;

    /**
     * @param string $teamId
     * @param string $email
     */
    private function __construct(string $teamId, string $email)
    {
        $this->teamId = $teamId;
        $this->email = $email;
    }

    /**
     * @param TeamParticipant $teamParticipant
     * @return self
     */
    public static function fromTeamParticipant(
        TeamParticipant $teamParticipant
    ): self {
        return new self(
            $teamParticipant->getTeamId()->toString(),
            $teamParticipant->getEmail()->toNative()
        );
    }
}
