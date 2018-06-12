<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\Entities\CompanyEntity;

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
    public function getAll(): ?Companies
    {
        /** @var CompanyEntity[] $companyEntities */
        $companyEntities = $this->objectRepository->findAll();

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
