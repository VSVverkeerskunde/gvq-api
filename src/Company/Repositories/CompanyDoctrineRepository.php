<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\Entities\CompanyEntity;
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
     * @param Company $company
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
     * @param UuidInterface $id
     * @return Company|null
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
}
