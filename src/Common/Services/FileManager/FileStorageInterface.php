<?php

namespace Project\Common\Services\FileManager;

interface FileStorageInterface
{
    public function save(File $file, string $to): File;

    public function delete(string $filePath): void;

    public function exists(string $filePath): bool;

    public function url(string $filePath): string;
}