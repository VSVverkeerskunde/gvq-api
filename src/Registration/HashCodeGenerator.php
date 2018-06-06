<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration;

class HashCodeGenerator
{
    /**
     * @return string
     */
    public static function generateCode(): string
    {
        return bin2hex(random_bytes(22));
    }
}
