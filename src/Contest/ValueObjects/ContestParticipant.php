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
}
