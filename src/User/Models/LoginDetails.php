<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

use VSV\GVQ_API\User\ValueObjects\Email;

class LoginDetails
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (!isset($values['email']) || !isset($values['password'])) {
            throw new \InvalidArgumentException('No values supplied for email or password');
        }
        $this->email = new Email($values['email']);
        $this->password = $values['password'];
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
