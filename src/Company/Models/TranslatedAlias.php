<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class TranslatedAlias
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Alias
     */
    private $alias;

    /**
     * @var Language
     */
    private $language;

    /**
     * @param UuidInterface $id
     * @param Alias $alias
     * @param Language $language
     */
    public function __construct(UuidInterface $id, Language $language, Alias $alias)
    {
        $this->id = $id;
        $this->language = $language;
        $this->alias = $alias;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Alias
     */
    public function getAlias(): Alias
    {
        return $this->alias;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }
}
