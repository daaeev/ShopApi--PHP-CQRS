<?php

namespace Project\Common\Services\FileManager\DirectoryNameGenerators;

use Project\Common\Services\FileManager\DirectoryNameGeneratorInterface;

class BaseDirectoryNameGenerator implements DirectoryNameGeneratorInterface
{
    public function __construct(
        private string $relativeBaseDir = ''
    ) {}

    public function generateDirectoryName(
        string $fileName,
        array $options = []
    ): string {
        return $this->relativeBaseDir;
    }
}