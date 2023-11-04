<?php

namespace Project\Common\Services\FileManager\FileNameGenerators;

use Project\Common\Services\FileManager\FileNameGeneratorInterface;

class TimestampPrefixFileNameGenerator implements FileNameGeneratorInterface
{
    public function generateName(string $baseFileName, array $options = []): string
    {
        return time() . '_' . $baseFileName;
    }
}