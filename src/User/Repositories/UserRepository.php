<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Models\Users;
use VSV\GVQ_API\User\ValueObjects\Email;

interface UserRepository
{
    public function save(User $user): void;

    public function getById(UuidInterface $id): ?User;

    public function getByEmail(Email $email): ?User;

    public function getAll(): ?Users;

    public function update(User $user): void;
}
