<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\ValueObjects;

interface UrlSuffixGenerator
{
    /**
     * @return UrlSuffix
     */
    public function createUrlSuffix(): UrlSuffix;
}
