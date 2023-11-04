<?php

namespace Project\Common\Services\FileManager;

class File
{
    public function __construct(
        public readonly string $fullPath,
        public readonly string $fileName,
        public readonly string $content = '',
    ) {}
}