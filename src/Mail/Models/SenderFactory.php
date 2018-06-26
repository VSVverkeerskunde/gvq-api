<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Models;

use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\ValueObjects\Email;

class SenderFactory
{
    /**
     * @param string $email
     * @param string $name
     * @return Sender
     */
    public static function fromNative(string $email, string $name): Sender
    {
        return new Sender(
            new Email($email),
            new NotEmptyString($name)
        );
    }
}
