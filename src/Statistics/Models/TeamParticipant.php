<?php
/**
 * @file
 */

namespace VSV\GVQ_API\Statistics\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\User\ValueObjects\Email;

class TeamParticipant
{
    /**
     * @var UuidInterface
     */
    private $teamId;

    /**
     * @var Email
     */
    private $email;

    /**
     * @param UuidInterface $teamId
     * @param Email $email
     */
    public function __construct(UuidInterface $teamId, Email $email)
    {
        $this->teamId = $teamId;
        $this->email = $email;
    }

    /**
     * @return UuidInterface
     */
    public function getTeamId(): UuidInterface
    {
        return $this->teamId;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
}
