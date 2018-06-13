<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\Entities\CompanyEntity;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\User\Repositories\Entities\UserEntity;

class CompanyDoctrineRepository extends AbstractDoctrineRepository implements CompanyRepository
{
    /**
     * @inheritdoc
     */
    public function getRepositoryName(): string
    {
        return CompanyEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function save(Company $company): void
    {
        $companyEntity = CompanyEntity::fromCompany($company);

        /** @var UserEntity $userEntity */
        $userEntity = $this->entityManager->find(
            UserEntity::class,
            $companyEntity->getUserEntity()->getId()
        );

        if ($userEntity == null) {
            throw new InvalidArgumentException(
                'User with id: '.
                $companyEntity->getUserEntity()->getId().
                ' not found.'
            );
        }

        $companyEntity->setUserEntity($userEntity);

        $this->entityManager->persist($companyEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getById(UuidInterface $id): ?Company
    {
        /** @var CompanyEntity|null $companyEntity */
        $companyEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $companyEntity ? $companyEntity->toCompany() : null;
    }

    /**
     * @inheritdoc
     */
    public function getByName(NotEmptyString $name): ?Company
    {
        /** @var CompanyEntity $companyEntity */
        $companyEntity = $this->objectRepository->findOneBy(
            [
                'name' => $name->toNative(),
            ]
        );

        return $companyEntity ? $companyEntity->toCompany() : null;
    }

    /**
     * @inheritdoc
     */
    public function getByAlias(Alias $alias): ?Company
    {
        //@todo: find better way to find a company by alias
        /** @var CompanyEntity[] $companyEntities */
        $companyEntities = $this->objectRepository->findAll();
        foreach ($companyEntities as $companyEntity) {
            $translatedAliasesEntities = $companyEntity->getTranslatedAliasEntities();
            foreach ($translatedAliasesEntities as $translatedAliasEntity) {
                if ($translatedAliasEntity->getAlias() === $alias->toNative()) {
                    return $companyEntity->toCompany();
                }
            }
        }

        return null;
    }
}
