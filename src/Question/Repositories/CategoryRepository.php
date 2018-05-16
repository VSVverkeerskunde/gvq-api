<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;

interface CategoryRepository
{
    public function save(Category $category): void;

    public function update(Category $category): void;

    public function delete(Category $category): void;

    public function getById(UuidInterface $id): ?Category;

    public function getAll(): ?Categories;
}
