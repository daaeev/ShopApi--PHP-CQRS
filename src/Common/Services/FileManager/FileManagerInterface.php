<?php

namespace Project\Common\Services\FileManager;

interface FileManagerInterface
{
    public function save(File $file): File;

    public function delete(string $fileName): void;

    public function exists(string $fileName): bool;

    public function url(?string $fileName): ?string;
}