<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class CategoryDifficultyRedisRepository extends AbstractRedisRepository implements CategoryDifficultyRepository
{
    /**
     * @var NotEmptyString
     */
    private $key;

    /**
     * @param \Redis $redis
     * @param NotEmptyString $key
     */
    public function __construct(
        \Redis $redis,
        NotEmptyString $key
    ) {
        parent::__construct($redis);

        $this->key = $key;
    }

    /**
     * @inheritdoc
     */
    public function increment(Category $category, Language $language): void
    {
        $this->redis->incr($this->createKey($category, $language));
    }

    /**
     * @inheritdoc
     */
    public function getCount(Category $category, Language $language): NaturalNumber
    {
        $count = $this->redis->get($this->createKey($category, $language));
        return new NaturalNumber((int)$count);
    }

    /**
     * @param Category $category
     * @param Language $language
     * @return string
     */
    private function createKey(Category $category, Language $language): string
    {
        return $category->getId()->toString().'_'.$this->key->toNative().'_'.$language->toNative();
    }
}
