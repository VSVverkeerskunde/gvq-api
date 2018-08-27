<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Contest\ValueObjects\ContestParticipant;
use VSV\GVQ_API\User\ValueObjects\Email;

/**
 * @ORM\Embeddable()
 */
class ContestParticipantEmbeddable
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", name="date_of_birth", nullable=false)
     */
    private $dateOfBirth;

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param \DateTimeImmutable $dateOfBirth
     */
    private function __construct(
        string $email,
        string $firstName,
        string $lastName,
        \DateTimeImmutable $dateOfBirth
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @param ContestParticipant $contestParticipant
     * @return ContestParticipantEmbeddable
     */
    public static function fromContestParticipation(
        ContestParticipant $contestParticipant
    ): ContestParticipantEmbeddable {
        return new ContestParticipantEmbeddable(
            $contestParticipant->getEmail()->toNative(),
            $contestParticipant->getFirstName()->toNative(),
            $contestParticipant->getLastName()->toNative(),
            $contestParticipant->getDateOfBirth()
        );
    }

    /**
     * @return ContestParticipant
     */
    public function toContestParticipant(): ContestParticipant
    {
        return new ContestParticipant(
            new Email($this->email),
            new NotEmptyString($this->firstName),
            new NotEmptyString($this->lastName),
            $this->dateOfBirth
        );
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateOfBirth(): \DateTimeImmutable
    {
        return $this->dateOfBirth;
    }
}
