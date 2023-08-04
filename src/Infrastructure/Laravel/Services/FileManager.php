<?php

namespace Project\Infrastructure\Laravel\Services;

use Webmozart\Assert\Assert;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Project\Common\Services\FileManager\Disk;
use Project\Common\Services\FileManager\File;
use Project\Common\Services\FileManager\FileManagerInterface;

class FileManager implements FileManagerInterface
{
    public function save(
        mixed $file,
        string $directory = '',
        Disk $disk = Disk::PUBLIC
    ): File {
        Assert::isInstanceOf($file, UploadedFile::class, 'File must be instance of ' . UploadedFile::class);

        $fullPath = Storage::disk($this->resolveDiskName($disk))->put($directory, $file);
        $explodedFullPath = explode('/', $fullPath);
        $fileName = $explodedFullPath[array_key_last($explodedFullPath)];

        return new File($fullPath, $fileName, $disk);
    }

    private function resolveDiskName(Disk $disk): string
    {
        return $disk->value;
    }

    public function delete(string $file, Disk $disk = Disk::PUBLIC): void
    {
        if ($this->exists($file, $disk)) {
            Storage::disk($this->resolveDiskName($disk))->delete($file);
        }
    }

    public function exists(string $file, Disk $disk = Disk::PUBLIC): bool
    {
        return Storage::disk($this->resolveDiskName($disk))->exists($file);
    }

    public function fullPath(string $file, Disk $disk = Disk::PUBLIC): string
    {
        return str_replace(
            '\\',
            '/',
            Storage::disk($this->resolveDiskName($disk))->url($file)
        );
    }
}