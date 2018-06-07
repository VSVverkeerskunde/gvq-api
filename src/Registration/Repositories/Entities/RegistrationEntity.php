<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\Entities\Entity;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;
use VSV\GVQ_API\User\Repositories\Entities\UserEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="registration", indexes={
 *     @ORM\Index(name="url_suffix_idx", columns={"url_suffix"}),
 *     @ORM\Index(name="user_id_idx", columns={"user_id"})
 * })
 */
class RegistrationEntity extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="url_suffix", length=22, nullable=false, unique=true)
     */
    private $urlSuffix;

    /**
     * @var UserEntity
     *
     * @ORM\OneToOne(targetEntity="VSV\GVQ_API\User\Repositories\Entities\UserEntity", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, unique=true)
     */
    private $userEntity;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime",name="created_on", nullable=false)
     */
    private $createdOn;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="password_reset", nullable=false)
     */
    private $passwordReset;

    /**
     * @param string $id
     * @param string $hashCode
     * @param UserEntity $userEntity
     * @param \DateTimeImmutable $createdOn
     * @param bool $passwordReset
     */
    public function __construct(
        string $id,
        string $hashCode,
        UserEntity $userEntity,
        \DateTimeImmutable $createdOn,
        bool $passwordReset
    ) {
        parent::__construct($id);
        $this->urlSuffix = $hashCode;
        $this->userEntity = $userEntity;
        $this->createdOn = $createdOn;
        $this->passwordReset = $passwordReset;
    }


    /**
     * @param Registration $registration
     * @return RegistrationEntity
     */
    public static function fromRegistration(Registration $registration): RegistrationEntity
    {
        return new RegistrationEntity(
            $registration->getId()->toString(),
            $registration->getUrlSuffix()->toNative(),
            UserEntity::fromUser($registration->getUser()),
            $registration->getCreatedOn(),
            $registration->isPasswordReset()
        );
    }

    /**
     * @return Registration
     */
    public function toRegistration(): Registration
    {
        return new Registration(
            Uuid::fromString($this->getId()),
            new UrlSuffix($this->getUrlSuffix()),
            $this->getUserEntity()->toUser(),
            $this->getCreatedOn(),
            $this->isPasswordReset()
        );
    }

    /**
     * @return string
     */
    public function getUrlSuffix(): string
    {
        return $this->urlSuffix;
    }

    /**
     * @return UserEntity
     */
    public function getUserEntity(): UserEntity
    {
        return $this->userEntity;
    }

    /**
     * @param UserEntity $userEntity
     */
    public function setUserEntity(UserEntity $userEntity): void
    {
        $this->userEntity = $userEntity;
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
