<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\Entities\CompanyEntity;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\User\Models\User;
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

        // The user object inside company is not managed,
        // therefore we need to use merge instead of persist.
        // When user wouldn't exist yet, the user is not created.
        $this->entityManager->merge($companyEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     * @throws EntityNotFoundException
     */
    public function update(Company $company): void
    {
        // Make sure the company exists,
        // otherwise the merge would create a new company.
        $companyEntity = $this->entityManager->find(
            CompanyEntity::class,
            $company->getId()
        );
        if ($companyEntity == null) {
            throw new EntityNotFoundException("Invalid company supplied");
        }

        $this->entityManager->merge(
            CompanyEntity::fromCompany($company)
        );
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByAlias(Alias $alias): ?Company
    {
        $companyEntity = $this->entityManager->createQueryBuilder()
            ->select('c, u')
            ->from(CompanyEntity::class, 'c')
            ->innerJoin('c.translatedAliasEntities', 'a')
            ->innerJoin('c.userEntity', 'u')
            ->where('a.alias = :alias')
            ->setParameter('alias', $alias->toNative())
            ->getQuery()
            ->getOneOrNullResult();

        return $companyEntity ? $companyEntity->toCompany() : null;
    }

    /**
     * @inheritdoc
     */
    public function getAll(): ?Companies
    {
        /** @var CompanyEntity[] $companyEntities */
        $companyEntities = $this->objectRepository->findAll();

        return $this->toCompanies($companyEntities);
    }

    /**
     * @inheritdoc
     */
    public function getAllByUser(User $user): ?Companies
    {
        /** @var CompanyEntity[] $companyEntities */
        $companyEntities = $this->objectRepository->findBy(
            [
                'userEntity' => UserEntity::fromUser($user),
            ]
        );

        return $this->toCompanies($companyEntities);
    }

    /**
     * @param CompanyEntity[] $companyEntities
     * @return null|Companies
     */
    private function toCompanies(array $companyEntities): ?Companies
    {
        if (empty($companyEntities)) {
            return null;
        }

        return new Companies(
            ...array_map(
                function (CompanyEntity $companyEntity) {
                    return $companyEntity->toCompany();
                },
                $companyEntities
            )
        );
    }
}
