<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuizCompositionYamlRepository implements QuizCompositionRepository
{
    /**
     * @var mixed
     */
    private $quizCompositionAsYml;

    public function __construct(string $quicCompositionFile)
    {
        $this->quizCompositionAsYml = Yaml::parseFile($quicCompositionFile);
    }

    /**
     * @inheritdoc
     */
    public function getByYearAndCategory(Year $year, Category $category): ?int
    {
        if (!key_exists($year->toNative(), $this->quizCompositionAsYml) ||
            !key_exists($category->getId()->toString(), $this->quizCompositionAsYml[$year->toNative()])
        ) {
            return null;
        }

        return $this->quizCompositionAsYml[$year->toNative()][$category->getId()->toString()];
    }
}
