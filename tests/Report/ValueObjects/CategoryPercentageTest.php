<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Statistics\ValueObjects\Percentage;

class CategoryPercentageTest extends TestCase
{
    /**
     * @var CategoryPercentage
     */
    private $categoryPercentage;

    protected function setUp(): void
    {
        $this->categoryPercentage = new CategoryPercentage(
            ModelsFactory::createGeneralCategory(),
            new Language(Language::NL),
            new Percentage(0.60)
        );
    }

    /**
     * @test
     */
    public function it_stores_a_category(): void
    {
        $this->assertEquals(
            ModelsFactory::createGeneralCategory(),
            $this->categoryPercentage->getCategory()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_language(): void
    {
        $this->assertEquals(
            new Language(Language::NL),
            $this->categoryPercentage->getLanguage()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_percentage(): void
    {
        $this->assertEquals(
            new Percentage(0.60),
            $this->categoryPercentage->getPercentage()
        );
    }
}
