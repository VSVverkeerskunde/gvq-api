<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Team\Models\Team;
use VSV\GVQ_API\Team\Models\Teams;

interface TeamRepository
{
    /**
     * @param Year $year
     * @param UuidInterface $uuid
     * @return Team|null
     */
    public function getByYearAndId(Year $year, UuidInterface $uuid): ?Team;

    /**
     * @param Year $year
     * @return Teams
     */
    public function getAllByYear(Year $year): Teams;
}
