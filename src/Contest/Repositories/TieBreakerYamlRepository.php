<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Contest\Models\TieBreaker;
use VSV\GVQ_API\Contest\Models\TieBreakers;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class TieBreakerYamlRepository implements TieBreakerRepository
{
    /**
     * @var array
     */
    private $tieBreakersAsYaml;

    /**
     * @param string $tieBreakersFile
     */
    public function __construct(string $tieBreakersFile)
    {
        $this->tieBreakersAsYaml = Yaml::parseFile($tieBreakersFile);
    }

    /**
     * @inheritdoc
     */
    public function getAllByYear(Year $year): ?TieBreakers
    {
        if (!key_exists($year->toNative(), $this->tieBreakersAsYaml)) {
            return null;
        }

        $tieBreakersArray = [];
        foreach ($this->tieBreakersAsYaml[$year->toNative()] as $tieBreakerAsYaml) {
            $tieBreakersArray[] = new TieBreaker(
                Uuid::fromString($tieBreakerAsYaml['id']),
                $year,
                new QuizChannel($tieBreakerAsYaml['channel']),
                new Language($tieBreakerAsYaml['language']),
                new NotEmptyString($tieBreakerAsYaml['question']),
                $tieBreakerAsYaml['answer'] === null ? null : new PositiveNumber($tieBreakerAsYaml['answer'])
            );
        }

        return new TieBreakers(...$tieBreakersArray);
    }
}
