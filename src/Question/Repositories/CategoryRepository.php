<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;

interface CategoryRepository
{
    public function save(Category $question): void;

    public function update(Category $question): void;

    public function delete(Category $question): void;

    public function getById(UuidInterface $id): ?Categories;

    public function getAll(): ?Category;
}
