<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\Entities\Entity;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\User\Repositories\Entities\UserEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="company")
 */
class CompanyEntity extends Entity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $numberOfEmployees;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="TranslatedAliasEntity", mappedBy="companyEntity", cascade={"all"}, fetch="EAGER")
     *
     */
    private $translatedAliasEntities;

    /**
     * @var UserEntity
     * @ORM\ManyToOne(targetEntity="VSV\GVQ_API\User\Repositories\Entities\UserEntity", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $userEntity;

    /**
     * @param string $id
     * @param string $name
     * @param int $numberOfEmployees
     * @param Collection $translatedAliasEntities
     * @param UserEntity $user
     */
    public function __construct(
        string $id,
        string $name,
        int $numberOfEmployees,
        Collection $translatedAliasEntities,
        UserEntity $user
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->numberOfEmployees = $numberOfEmployees;
        $this->translatedAliasEntities = $translatedAliasEntities;
        $this->userEntity = $user;

        foreach ($translatedAliasEntities as $translatedAliasEntity) {
            $translatedAliasEntity->setCompanyEntity($this);
        }
    }

    /**
     * @param Company $company
     * @return CompanyEntity
     */
    public static function fromCompany(Company $company): CompanyEntity
    {
        /** @var TranslatedAliasEntity[] $translatedAliasEntities */
        $translatedAliasEntities = array_map(
            function (TranslatedAlias $translatedAlias) {
                return TranslatedAliasEntity::fromTranslatedAlias($translatedAlias);
            },
            $company->getTranslatedAliases()->toArray()
        );

        $companyEntity = new CompanyEntity(
            $company->getId()->toString(),
            $company->getName()->toNative(),
            $company->getNumberOfEmployees()->toNative(),
            new ArrayCollection($translatedAliasEntities),
            UserEntity::fromUser($company->getUser())
        );

        return $companyEntity;
    }

    /**
     * @return Company
     */
    public function toCompany(): Company
    {
        $translatedAliases = new TranslatedAliases(
            ...array_map(
                function (TranslatedAliasEntity $translatedAliasEntity) {
                    return $translatedAliasEntity->toTranslatedAlias();
                },
                $this->getTranslatedAliasEntities()->toArray()
            )
        );

        return new Company(
            Uuid::fromString($this->getId()),
            new NotEmptyString($this->getName()),
            new PositiveNumber($this->getNumberOfEmployees()),
            $translatedAliases,
            $this->getUserEntity()->toUser()
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNumberOfEmployees(): int
    {
        return $this->numberOfEmployees;
    }

    /**
     * @return Collection
     */
    public function getTranslatedAliasEntities(): Collection
    {
        return $this->translatedAliasEntities;
    }

    /**
     * @return UserEntity
     */
    public function getUserEntity(): UserEntity
    {
        return $this->userEntity;
    }
}
