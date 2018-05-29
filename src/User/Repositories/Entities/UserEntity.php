<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories\Entities;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\Entities\Entity;
use Doctrine\ORM\Mapping as ORM;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class UserEntity extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $role;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @param string $id
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string $role
     * @param string|null $password
     */
    public function __construct(
        string $id,
        string $email,
        string $lastName,
        string $firstName,
        string $role,
        ?string $password
    ) {
        parent::__construct($id);

        $this->email = $email;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->role = $role;
        $this->password = $password;
    }

    /**
     * @param User $user
     * @return UserEntity
     */
    public static function fromUser(User $user): UserEntity
    {
        return new UserEntity(
            $user->getId()->toString(),
            $user->getEmail()->toNative(),
            $user->getLastName()->toNative(),
            $user->getFirstName()->toNative(),
            $user->getRole()->toNative(),
            $user->getPassword() ? $user->getPassword()->toNative() : null
        );
    }

    /**
     * @return User
     */
    public function toUser(): User
    {
        $user = new User(
            Uuid::fromString($this->getId()),
            new Email($this->getEmail()),
            new NotEmptyString($this->getLastName()),
            new NotEmptyString($this->getFirstName()),
            new Role($this->getRole())
        );

        if ($this->getPassword()) {
            $user = $user->withPassword(
                Password::fromHash($this->getPassword())
            );
        }

        return $user;
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
    public function getLastName(): string
    {
        return $this->lastName;
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
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
