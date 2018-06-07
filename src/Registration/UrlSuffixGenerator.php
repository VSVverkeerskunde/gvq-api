<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration;

use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;

interface UrlSuffixGenerator
{
    public function createUrlSuffix(): UrlSuffix;
}
