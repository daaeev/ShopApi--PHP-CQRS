<?php

namespace Project\Common\Services\FileManager;

class File
{
    public function __construct(
        private string $fullPath,
        private string $fileName,
        private Disk $disk
    ) {}

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getDisk(): Disk
    {
        return $this->disk;
    }
}