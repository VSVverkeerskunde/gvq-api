<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;
use VSV\GVQ_API\User\Models\User;

class Registration
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var UrlSuffix
     */
    private $urlSuffix;

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
     * @param UrlSuffix $hashCode
     * @param User $user
     * @param \DateTimeImmutable $createdOn
     * @param bool $passwordReset
     */
    public function __construct(
        UuidInterface $id,
        UrlSuffix $hashCode,
        User $user,
        \DateTimeImmutable $createdOn,
        bool $passwordReset
    ) {
        $this->id = $id;
        $this->urlSuffix = $hashCode;
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
     * @return UrlSuffix
     */
    public function getUrlSuffix(): UrlSuffix
    {
        return $this->urlSuffix;
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
