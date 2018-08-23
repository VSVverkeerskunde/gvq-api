<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Contest\Models\TieBreakers;

class TieBreakerYamlRepository implements TieBreakerRepository
{
    /**
     * @var array
     */
    private $tieBreakersAsYaml;

    /**
     * @param $tieBreakersFile
     */
    public function __construct($tieBreakersFile)
    {
        $this->tieBreakersAsYaml = Yaml::parseFile($tieBreakersFile);
    }

    /**
     * @inheritdoc
     */
    public function getAllByYear(int $year): ?TieBreakers
    {
        
    }
}
