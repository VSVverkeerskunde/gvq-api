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
     * @var Password
     */
    private $password;

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
     * @param UuidInterface $id
     * @param Email $email
     * @param Password $password
     * @param NotEmptyString $lastName
     * @param NotEmptyString $firstName
     * @param Role $role
     */
    public function __construct(
        UuidInterface $id,
        Email $email,
        Password $password,
        NotEmptyString $lastName,
        NotEmptyString $firstName,
        Role $role
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
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
     * @return Password
     */
    public function getPassword(): Password
    {
        return $this->password;
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
}
