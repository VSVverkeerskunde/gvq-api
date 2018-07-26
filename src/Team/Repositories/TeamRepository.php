<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Team\Models\Team;

interface TeamRepository
{
    /**
     * @param Year $year
     * @param UuidInterface $uuid
     * @return null|Team
     */
    public function getByYearAndId(Year $year, UuidInterface $uuid): ?Team;
}
