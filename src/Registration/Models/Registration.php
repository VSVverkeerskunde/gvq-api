<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\User\Models\User;

class Registration
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $hashCode;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \DateTimeImmutable
     */
    private $createdOn;

    /**
     * @var bool
     */
    private $passwordReset;

    /**
     * @param UuidInterface $id
     * @param string $hashCode
     * @param User $user
     * @param \DateTimeImmutable $createdOn
     * @param bool $passwordReset
     */
    public function __construct(
        UuidInterface $id,
        string $hashCode,
        User $user,
        \DateTimeImmutable $createdOn,
        bool $passwordReset
    ) {
        $this->id = $id;
        $this->hashCode = $hashCode;
        $this->user = $user;
        $this->createdOn = $createdOn;
        $this->passwordReset = $passwordReset;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHashCode(): string
    {
        return $this->hashCode;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedOn(): \DateTimeImmutable
    {
        return $this->createdOn;
    }

    /**
     * @return bool
     */
    public function isPasswordReset(): bool
    {
        return $this->passwordReset;
    }
}
