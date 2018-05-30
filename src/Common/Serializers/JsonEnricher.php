<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\Serializers;

interface JsonEnricher
{
    /**
     * @param string $json
     * @return string
     */
    public function enrich(string $json): string;
}
