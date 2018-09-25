<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class CategoryDifficultyRedisRepositoryTest extends TestCase
{
    /**
     * @var \Redis|MockObject
     */
    private $redis;

    /**
     * @var CategoryDifficultyRedisRepository
     */
    private $categoryDifficultyRedisRepository;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var string
     */
    private $key;

    protected function setUp(): void
    {
        /** @var \Redis|MockObject $redis */
        $redis = $this->createMock(\Redis::class);
        $this->redis = $redis;

        $this->categoryDifficultyRedisRepository = new CategoryDifficultyRedisRepository(
            $this->redis,
            new NotEmptyString('answered_correct')
        );

        $this->category = ModelsFactory::createGeneralCategory();
        $this->language = new Language(Language::NL);
        $this->key = $this->category->getId()->toString().'_'.'answered_correct'.'_'.$this->language->toNative();
    }

    /**
     * @test
     */
    public function it_can_increment_count(): void
    {
        $this->redis->expects($this->once())
            ->method('incr')
            ->with($this->key);

        $this->categoryDifficultyRedisRepository->increment(
            $this->category,
            $this->language
        );
    }

    /**
     * @test
     */
    public function it_can_get_count(): void
    {
        $this->redis->expects($this->once())
            ->method('get')
            ->with($this->key)
            ->willReturn(2);

        $actualCount = $this->categoryDifficultyRedisRepository->getCount(
            $this->category,
            $this->language
        );

        $this->assertEquals(new NaturalNumber(2), $actualCount);
    }
}
