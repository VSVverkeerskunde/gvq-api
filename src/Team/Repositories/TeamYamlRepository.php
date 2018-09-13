<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Repositories;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Team\Models\Team;
use VSV\GVQ_API\Team\Models\Teams;

class TeamYamlRepository implements TeamRepository
{
    /**
     * @var array
     */
    private $teamsAsYml;

    /**
     * @param string $teamFile
     */
    public function __construct(string $teamFile)
    {
        $this->teamsAsYml = Yaml::parseFile($teamFile);
    }

    /**
     * @inheritdoc
     */
    public function getByYearAndId(Year $year, UuidInterface $uuid): ?Team
    {
        if (!key_exists($year->toNative(), $this->teamsAsYml)) {
            return null;
        }

        if (key_exists($uuid->toString(), $this->teamsAsYml[$year->toNative()])) {
            return new Team(
                $uuid,
                new NotEmptyString($this->teamsAsYml[$year->toNative()][$uuid->toString()]['name'])
            );
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getAllByYear(Year $year): ?Teams
    {
        if (!key_exists($year->toNative(), $this->teamsAsYml)) {
            return null;
        }

        $teams = [];

        foreach ($this->teamsAsYml[$year->toNative()] as $key => $teamAsYml) {
            $teams[] = new Team(
                Uuid::fromString($key),
                new NotEmptyString($teamAsYml['name'])
            );
        }

        return new Teams(...$teams);
    }
}
