<?php declare(strict_types=1);

namespace VSV\GVQ_API\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
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
        /** @var TranslatedAlias $translatedAlias */
        foreach ($translatedAliases as $translatedAlias) {
            if ($translatedAlias->getLanguage()->toNative() === $language) {
                return $translatedAlias->getAlias()->toNative();
            }
        }

        return '';
    }
}
