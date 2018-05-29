<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\Entities\Entity;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\ValueObjects\Alias;

/**
 * @ORM\Entity()
 * @ORM\Table(name="translated_alias")
 */
class TranslatedAliasEntity extends Entity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $alias;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $language;

    /**
     * @var CompanyEntity
     * @ORM\ManyToOne(targetEntity="CompanyEntity", inversedBy="translatedAliasEntities")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $companyEntity;

    /**
     * @param string $id
     * @param string $language
     * @param string $alias
     */
    public function __construct(
        string $id,
        string $language,
        string $alias
    ) {
        parent::__construct($id);

        $this->language = $language;
        $this->alias = $alias;
    }

    /**
     * @param TranslatedAlias $translatedAlias
     * @return TranslatedAliasEntity
     */
    public static function fromTranslatedAlias(TranslatedAlias $translatedAlias): TranslatedAliasEntity
    {
        return new TranslatedAliasEntity(
            $translatedAlias->getId()->toString(),
            $translatedAlias->getLanguage()->toNative(),
            $translatedAlias->getAlias()->toNative()
        );
    }

    /**
     * @return TranslatedAlias
     */
    public function toTranslatedAlias(): TranslatedAlias
    {
        return new TranslatedAlias(
            Uuid::fromString($this->getId()),
            new Language($this->getLanguage()),
            new Alias($this->getAlias())
        );
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param CompanyEntity $companyEntity
     */
    public function setCompanyEntity(CompanyEntity $companyEntity): void
    {
        $this->companyEntity = $companyEntity;
    }
}
