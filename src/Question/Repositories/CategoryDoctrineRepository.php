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
        $this->objectRepository = $this->entityManager->getRepository(Category::class);
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Category $category): void
    {
        $this->entityManager->merge($category);
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Category $category): void
    {
        $category = $this->entityManager->merge($category);
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $id
     * @return Category|null
     */
    public function getById(UuidInterface $id): ?Category
    {
        /** @var Category|null $category */
        $category = $this->objectRepository->findOneBy(
            [
                'id' => $id
            ]
        );

        return $category;
    }

    /**
     * @return Categories|null
     */
    public function getAll(): ?Categories
    {
        /** @var Category[] $categories */
        $categories = $this->objectRepository->findAll();

        if (empty($categories)) {
            return null;
        }

        return new Categories(...$categories);
    }
}
