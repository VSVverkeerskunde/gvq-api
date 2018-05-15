<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;

class CategoryDoctrineRepository extends AbstractDoctrineRepository implements CategoryRepository
{
    /**
     * @inheritdoc
     */
    public function getRepositoryName(): string
    {
        return Category::class;
    }

    /**
     * @param Category $category
     */
    public function save(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     */
    public function update(Category $category): void
    {
        $this->entityManager->merge($category);
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
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
                'id' => $id,
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
