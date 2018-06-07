<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration;

use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;

class UrlSuffixHashGenerator implements UrlSuffixGenerator
{
    public function createUrlSuffix(): UrlSuffix
    {
        return new UrlSuffix(bin2hex(random_bytes(11)));
    }
}
