<?php

namespace Project\Common\Services\FileManager;

interface DirectoryNameGeneratorInterface
{
    public function generateDirectoryName(
        string $fileName,
        array $options = []
    ): string;
}