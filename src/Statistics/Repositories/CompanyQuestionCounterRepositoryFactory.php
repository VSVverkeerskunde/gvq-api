<?php

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class CompanyQuestionCounterRepositoryFactory extends AbstractRedisRepository
{
    /**
     * @var NotEmptyString
     */
    private $keyPrefix;

    /**
     * @param \Redis $redis
     * @param NotEmptyString $keyPrefix
     */
    public function __construct(
        \Redis $redis,
        NotEmptyString $keyPrefix
    ) {
        parent::__construct($redis);

        $this->keyPrefix = $keyPrefix;
    }

    public function forCompany(UuidInterface $companyId): QuestionCounterRedisRepository {
        return new QuestionCounterRedisRepository(
            $this->redis,
            new NotEmptyString(
                $this->keyPrefix->toNative() . '_company_' . $companyId->toString()
            )
        );
    }
}