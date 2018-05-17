<?php declare(strict_types=1);

namespace VSV\GVQ_API\Factory;

trait ExpectedJsonTrait
{
    /**
     * @param string $fullPath
     * @return string
     */
    private function getExpectedJson(string $fullPath): string
    {
        $jsonWithFormatting = file_get_contents($fullPath);
        $jsonAsArray = json_decode($jsonWithFormatting, true);
        return json_encode($jsonAsArray);
    }
}
