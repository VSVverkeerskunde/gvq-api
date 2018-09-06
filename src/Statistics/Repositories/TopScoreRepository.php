<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Statistics\Models\AverageScore;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\User\ValueObjects\Email;

interface TopScoreRepository
{
    public function getByEmail(Email $email): ?TopScore;

    public function save(TopScore $topScore): void;

    public function getAverageScoreForCompany(UuidInterface $companyId): AverageScore;
}
