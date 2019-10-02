<?php

namespace VSV\GVQ_API\Statistics\Repositories\Entities;

use Doctrine\Orm\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="participant_quiz"
 * )
 */
class ParticipantQuizEntity
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    private $email;

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $quiz_uuid;

    public function __construct(string $email, string $quiz_uuid)
    {
        $this->email = $email;
        $this->quiz_uuid = $quiz_uuid;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getQuizUuid(): string
    {
        return $this->quiz_uuid;
    }


}