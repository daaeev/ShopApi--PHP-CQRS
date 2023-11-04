<?php

namespace Project\Common\Services\FileManager;

class FileManager implements FileManagerInterface
{
    public function __construct(
        private DirectoryNameGeneratorInterface $directoryNameGenerator,
        private FileNameGeneratorInterface $fileNameGenerator,
        private FileStorageInterface $storage,
    ) {}

    public function save(File $file): File
    {
        $newFileName = $this->fileNameGenerator->generateName($file->fileName);
        $newFileDirectory = $this->directoryNameGenerator->generateDirectoryName($newFileName);
        $relativeFilePath = $newFileDirectory . '/' . $newFileName;
        return $this->storage->save($file, $relativeFilePath);
    }

    public function delete(string $fileName): void
    {
        $fileDirectory = $this->directoryNameGenerator->generateDirectoryName($fileName);
        $this->storage->delete($fileDirectory . '/' . $fileName);
    }

    public function exists(string $fileName): bool
    {
        $fileDirectory = $this->directoryNameGenerator->generateDirectoryName($fileName);
        return $this->storage->exists($fileDirectory . '/' . $fileName);
    }

    public function url(?string $fileName): ?string
    {
        if (null === $fileName) {
            return null;
        }

        $fileDirectory = $this->directoryNameGenerator->generateDirectoryName($fileName);
        return $this->storage->url($fileDirectory . '/' . $fileName);
    }
}