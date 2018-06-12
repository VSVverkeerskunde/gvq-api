<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Repositories\Entities\CategoryEntity;

class CategoryDoctrineRepository extends AbstractDoctrineRepository implements CategoryRepository
{
    /**
     * @inheritdoc
     */
    public function getRepositoryName(): string
    {
        return CategoryEntity::class;
    }

    /**
     * @param Category $category
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
     * @throws EntityNotFoundException
     */
    public function update(Category $category): void
    {
        // Make sure the category exists,
        // otherwise merge will create the category.
        $categoryEntity = $this->entityManager->find(
            CategoryEntity::class,
            $category->getId()
        );
        if ($categoryEntity == null) {
            throw new EntityNotFoundException("Invalid category supplied");
        }

        $this->entityManager->merge(
            CategoryEntity::fromCategory($category)
        );
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     */
    public function delete(Category $category): void
    {
        $category = $this->entityManager->merge(
            CategoryEntity::fromCategory($category)
        );
        $this->entityManager->remove($category);
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
                'id' => $id,
            ]
        );

        return $categoryEntity ? $categoryEntity->toCategory() : null;
    }

    /**
     * @return Categories|null
     */
    public function getAll(): ?Categories
    {
        /** @var CategoryEntity[] $categories */
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
