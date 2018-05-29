<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories;

use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;

interface UserRepository
{
    public function save(User $user): void;

    public function getByEmail(Email $email): ?User;
}
