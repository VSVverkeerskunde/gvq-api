<?php declare(strict_types=1);

namespace VSV\GVQ_API\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\TranslatedAliases;

class TranslatedAliasExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'getAliasByLanguage',
                [
                    $this,
                    'getAliasByLanguage',
                ]
            )
        ];
    }

    /**
     * @param TranslatedAliases $translatedAliases
     * @param string $language
     * @return string
     */
    public function getAliasByLanguage(
        TranslatedAliases $translatedAliases,
        string $language
    ): string {
        $foundAlias = $translatedAliases->getByLanguage(
            new Language($language)
        );

        return $foundAlias->getAlias()->toNative();
    }
}
