<?php

namespace Project\Common\Services\FileManager;

interface FileManagerInterface
{
    public function save(
        mixed $file,
        string $directory = '',
        Disk $disk = Disk::PUBLIC
    ): File;

    public function delete(string $file, Disk $disk = Disk::PUBLIC): void;

    public function exists(string $file, Disk $disk = Disk::PUBLIC): bool;

    public function fullPath(string $file, Disk $disk = Disk::PUBLIC): string;
}