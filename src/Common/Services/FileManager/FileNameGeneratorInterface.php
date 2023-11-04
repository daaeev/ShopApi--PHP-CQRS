<?php

namespace Project\Common\Services\FileManager;

interface FileNameGeneratorInterface
{
    public function generateName(string $baseFileName, array $options = []): string;
}