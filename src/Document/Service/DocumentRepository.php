<?php

namespace VSV\GVQ_API\Document\Service;

use Symfony\Component\Yaml\Yaml;

class DocumentRepository
{
    /**
     * @var string
     */
    private $configFilePath;

    public function __construct(string $configFilePath) {
        $this->configFilePath = $configFilePath;
    }

    public function getFiles(): array {
        return Yaml::parseFile($this->configFilePath);
    }
}