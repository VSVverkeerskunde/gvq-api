<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Repositories;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Partner\Models\Partners;
use VSV\GVQ_API\Question\ValueObjects\Year;

class PartnerYamlRepository implements PartnerRepository
{
    /**
     * @var array
     */
    private $partnersAsYml;

    /**
     * @param string $partnerFile
     */
    public function __construct(string $partnerFile)
    {
        $this->partnersAsYml = Yaml::parseFile($partnerFile);
    }

    /**
     * @inheritdoc
     */
    public function getByYearAndAlias(Year $year, Alias $alias): ?Partner
    {
        if (!key_exists($year->toNative(), $this->partnersAsYml)) {
            return null;
        }

        foreach ($this->partnersAsYml[$year->toNative()] as $partnerAsYml) {
            if ($partnerAsYml['alias'] === $alias->toNative()) {
                return new Partner(
                    Uuid::fromString($partnerAsYml['id']),
                    new NotEmptyString($partnerAsYml['name']),
                    new Alias($partnerAsYml['alias'])
                );
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getAllByYear(Year $year): ?Partners
    {
        if (!key_exists($year->toNative(), $this->partnersAsYml)) {
            return null;
        }

        $partners = [];

        foreach ($this->partnersAsYml[$year->toNative()] as $partnerAsYml) {
            $partners[] = new Partner(
                Uuid::fromString($partnerAsYml['id']),
                new NotEmptyString($partnerAsYml['name']),
                new Alias($partnerAsYml['alias'])
            );
        }

        return new Partners(...$partners);
    }
}
