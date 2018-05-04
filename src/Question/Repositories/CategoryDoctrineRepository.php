<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;

class CategoryDoctrineRepository implements CategoryRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $objectRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(
            CategoryEntity::class
        );
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Category $category): void
    {
        $this->entityManager->persist(
            CategoryEntity::fromCategory($category)
        );
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Category $category): void
    {
        $this->entityManager->merge(
            CategoryEntity::fromCategory($category)
        );
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Category $category): void
    {
        $categoryEntity = $this->entityManager->merge(
            CategoryEntity::fromCategory($category)
        );
        $this->entityManager->remove($categoryEntity);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $id
     * @return Category|null
     */
    public function getById(UuidInterface $id): ?Category
    {
        /** @var CategoryEntity|null $categoryEntity */
        $categoryEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id
            ]
        );

        if ($categoryEntity === null) {
            return null;
        }

        return $categoryEntity->toCategory();
    }

    /**
     * @return Categories|null
     */
    public function getAll(): ?Categories
    {
        $categoryEntities = $this->objectRepository->findAll();

        if (empty($categoryEntities)) {
            return null;
        }

        return new Categories(
            ...array_map(
                function (CategoryEntity $categoryEntity) {
                    return $categoryEntity->toCategory();
                },
                $categoryEntities
            )
        );
    }
}
