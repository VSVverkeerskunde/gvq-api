<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\User\ValueObjects\Email;

interface TopScoreRepository
{
    /**
     * @param TopScore $topScore
     *
     * Only store new top score when the given top score is higher then the current one.
     */
    public function saveWhenHigher(TopScore $topScore): void;

    /**
     * @param Email $email
     * @return null|TopScore
     */
    public function getByEmail(Email $email): ?TopScore;

    /**
     * @param UuidInterface $companyId
     * @return Average
     */
    public function getAverageForCompany(UuidInterface $companyId): Average;
}
