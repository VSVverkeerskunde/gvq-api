<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\ValueObjects;

class UrlSuffixHashGenerator implements UrlSuffixGenerator
{
    /**
     * @inheritdoc
     */
    public function createUrlSuffix(): UrlSuffix
    {
        return new UrlSuffix(bin2hex(random_bytes(22)));
    }
}
