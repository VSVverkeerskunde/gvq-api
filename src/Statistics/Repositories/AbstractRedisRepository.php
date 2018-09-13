<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

abstract class AbstractRedisRepository
{
    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }
}
