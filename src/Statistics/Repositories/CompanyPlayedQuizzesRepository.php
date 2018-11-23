<?php

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;

class CompanyPlayedQuizzesRepository extends AbstractRedisRepository
{
    public const KEY_PREFIX = 'company_';
    private const KEY_SUFFIX = '_played';

    /**
     * @inheritdoc
     */
    public function getPrefix(): string
    {
        return self::KEY_PREFIX;
    }

    /**
     * @inheritdoc
     */
    public function getCount(UuidInterface $companyId, Language $language): int
    {
        return (int)$this->redis->get($this->createKey($companyId, $language));
    }

    /**
     * @inheritdoc
     */
    public function incrementCount(UuidInterface $companyId, Language $language): void
    {
        $this->redis->incr($this->createKey($companyId, $language));
    }

    private function createKey(UuidInterface $companyId, Language $language): string
    {
        return self::KEY_PREFIX . $companyId->toString() . '_' . $language->toNative() . self::KEY_SUFFIX;
    }
}