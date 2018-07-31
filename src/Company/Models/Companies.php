<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Companies implements Collection
{
    /**
     * @var Company[]
     */
    private $companies;

    /**
     * @param Company ...$companies
     */
    public function __construct(Company ...$companies)
    {
        $this->companies = $companies;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->companies);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->companies);
    }

    /**
     * @return Company[]
     */
    public function toArray(): array
    {
        return $this->companies;
    }
}
