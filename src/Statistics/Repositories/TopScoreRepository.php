<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Statistics\Models\TopScores;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
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
     * @return TopScores
     */
    public function getAllByCompany(UuidInterface $companyId): TopScores;

    /**
     * @return Average
     */
    public function getAverage(): Average;

    /**
     * @param UuidInterface $companyId
     * @return Average
     */
    public function getAverageForCompany(UuidInterface $companyId): Average;

    /**
     * @param NaturalNumber $nrOfPassedEmployees
     * @return iterable
     */
    public function getTopCompanies(NaturalNumber $nrOfPassedEmployees): iterable;
}
