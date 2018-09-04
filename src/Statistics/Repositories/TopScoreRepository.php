<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Statistics\TopScore;
use VSV\GVQ_API\User\ValueObjects\Email;

interface TopScoreRepository
{
    public function findByEmail(Email $email): ?TopScore;

    public function set(TopScore $topScore): void;
}
