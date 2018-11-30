<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

interface EmployeeParticipationRepository
{
    /**
     * @param EmployeeParticipation $employee
     */
    public function save(EmployeeParticipation $employee): void;

    /**
     * @param UuidInterface $companyId
     * @return NaturalNumber
     */
    public function countByCompany(UuidInterface $companyId): NaturalNumber;

    public function countByCompanyAndLanguage(
        UuidInterface $companyId,
        Language $language
    ): int;


    public function countPassedByCompany(
        UuidInterface $companyId
    ): int;

    public function countPassedByCompanyAndLanguage(
        UuidInterface $companyId,
        Language $language
    ): int;

    /**
     * @inheritdoc
     */
    public function getAverageTopScoreForCompanyAndLanguage(
        UuidInterface $companyId,
        Language $language
    ): float;

    /**
     * @param \VSV\GVQ_API\User\ValueObjects\Email $email
     * @return iterable|EmployeeParticipation[]
     */
    public function getByEmail(Email $email): iterable;
}
