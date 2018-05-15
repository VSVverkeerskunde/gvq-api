<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

trait ExpectedJsonTrait
{
    /**
     * @param string $file
     * @return string
     */
    private function getExpectedJson(string $file): string
    {
        $jsonWithFormatting = file_get_contents(__DIR__ . '/Samples/'.$file);
        $jsonAsArray = json_decode($jsonWithFormatting, true);
        return json_encode($jsonAsArray);
    }
}
