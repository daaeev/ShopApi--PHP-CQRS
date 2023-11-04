<?php

namespace Project\Infrastructure\Laravel\Services;

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Filesystem;
use Project\Common\Services\FileManager\File;
use Project\Common\Services\FileManager\FileStorageInterface;

class LaravelStorage implements FileStorageInterface
{
    public function __construct(
        private Filesystem $storage
    ) {}

    public function save(File $file, string $to): File
    {
        $this->storage->put($to, $file->content);
        return new File(
            $this->storage->path($to),
            $file->fileName,
            $file->content
        );
    }

    public function delete(string $filePath): void
    {
        if ($this->storage->exists($filePath)) {
            $this->storage->delete($filePath);
        }
    }

    public function exists(string $filePath): bool
    {
        return $this->storage->exists($filePath);
    }

    public function url(string $filePath): string
    {
        if (!($this->storage instanceof Cloud)) {
            throw new \LogicException('Storage must be instance of Cloud for url creation');
        }
        return $this->storage->url($filePath);
    }
}