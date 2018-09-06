<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Statistics\ValueObjects\AverageScore;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\User\ValueObjects\Email;

interface TopScoreRepository
{
    /**
     * @param Email $email
     * @return null|TopScore
     */
    public function getByEmail(Email $email): ?TopScore;

    /**
     * @param TopScore $topScore
     */
    public function save(TopScore $topScore): void;

    /**
     * @param UuidInterface $companyId
     * @return AverageScore
     */
    public function getAverageScoreForCompany(UuidInterface $companyId): AverageScore;
}
