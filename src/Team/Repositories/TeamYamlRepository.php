<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Repositories;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Team\Models\Team;

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
}
