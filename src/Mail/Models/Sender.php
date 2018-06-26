<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Models;

use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\ValueObjects\Email;

class Sender
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var NotEmptyString
     */
    private $name;

    /**
     * @param Email $email
     * @param NotEmptyString $name
     */
    public function __construct(Email $email, NotEmptyString $name)
    {
        $this->email = $email;
        $this->name = $name;
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
    public function getName(): NotEmptyString
    {
        return $this->name;
    }
}
