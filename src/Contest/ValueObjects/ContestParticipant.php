<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\ValueObjects\Email;

class ContestParticipant
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var NotEmptyString
     */
    private $firstName;

    /**
     * @var NotEmptyString
     */
    private $lastName;

    /**
     * @var \DateTimeImmutable
     */
    private $dateOfBirth;

    /**
     * @param Email $email
     * @param NotEmptyString $firstName
     * @param NotEmptyString $lastName
     * @param \DateTimeImmutable $dateOfBirth
     */
    public function __construct(
        Email $email,
        NotEmptyString $firstName,
        NotEmptyString $lastName,
        \DateTimeImmutable $dateOfBirth
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return NotEmptyString
     */
    public function getFirstName(): NotEmptyString
    {
        return $this->firstName;
    }

    /**
     * @return NotEmptyString
     */
    public function getLastName(): NotEmptyString
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
