<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Repositories;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\ValueObjects\Year;

class PartnerYamlRepository implements PartnerRepository
{
    /**
     * @var string
     */
    private $partnerFile;

    /**
     * @param string $partnerFile
     */
    public function __construct(string $partnerFile)
    {
        $this->partnerFile = $partnerFile;
    }

    public function getByAliasandYear(Alias $alias, Year $year): ?Partner
    {
        $partnersAsYml = Yaml::parseFile($this->partnerFile);

        foreach ($partnersAsYml[$year->toNative()] as $partnerasYml) {
            if ($partnerasYml['alias'] === $alias->toNative()) {
                return new Partner(
                    Uuid::fromString($partnerasYml['id']),
                    new NotEmptyString($partnerasYml['name']),
                    new Alias($partnerasYml['alias'])
                );
            }
        }

        return null;
    }
}
