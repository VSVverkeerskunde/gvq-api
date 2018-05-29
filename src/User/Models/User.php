<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;

class User
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var NotEmptyString
     */
    private $lastName;

    /**
     * @var NotEmptyString
     */
    private $firstName;

    /**
     * @var Role
     */
    private $role;

    /**
     * @var Password|null
     */
    private $password;

    /**
     * @param UuidInterface $id
     * @param Email $email
     * @param NotEmptyString $lastName
     * @param NotEmptyString $firstName
     * @param Role $role
     */
    public function __construct(
        UuidInterface $id,
        Email $email,
        NotEmptyString $lastName,
        NotEmptyString $firstName,
        Role $role
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->role = $role;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
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
    public function getLastName(): NotEmptyString
    {
        return $this->lastName;
    }

    /**
     * @return NotEmptyString
     */
    public function getFirstName(): NotEmptyString
    {
        return $this->firstName;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @param Password $password
     * @return User
     */
    public function withPassword(Password $password): User
    {
        $c = clone $this;
        $c->password = $password;
        return $c;
    }

    /**
     * @return Password|null
     */
    public function getPassword(): ?Password
    {
        return $this->password;
    }
}
