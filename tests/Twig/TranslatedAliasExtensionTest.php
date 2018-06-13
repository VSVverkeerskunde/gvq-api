<?php declare(strict_types=1);

namespace VSV\GVQ_API\Twig;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class TranslatedAliasExtensionTest extends TestCase
{
    /**
     * @var TranslatedAliasExtension
     */
    private $translatedAliasExtension;

    protected function setUp(): void
    {
        $this->translatedAliasExtension = new TranslatedAliasExtension();
    }

    /**
     * @test
     */
    public function it_creates_a_filter()
    {
        $filters = $this->translatedAliasExtension->getFilters();
        /** @var \Twig_Filter $twigFilter */
        $twigFilter = $filters[0];

        $this->assertEquals($twigFilter->getName(), 'getAliasByLanguage');
    }

    /**
     * @test
     */
    public function it_can_filter_on_language()
    {
        $translatedAliases = ModelsFactory::createTranslatedAliases();

        $translatedAlias = $this->translatedAliasExtension->getAliasByLanguage(
            $translatedAliases,
            'fr'
        );

        $this->assertEquals('awsr', $translatedAlias);
    }
}
