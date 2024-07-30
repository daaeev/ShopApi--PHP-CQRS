<?php

namespace Project\Tests\Laravel\Services\FileManager;

use Project\Tests\Laravel\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Cloud;
use Project\Common\Services\FileManager\File;
use Project\Infrastructure\Laravel\Services\LaravelStorage;
use Project\Common\Services\FileManager\FileStorageInterface;

class LaravelStorageTest extends TestCase
{
    private FileStorageInterface $fileStorage;
    private Cloud $laravelStorageMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->laravelStorageMock = $this->getMockBuilder(Storage::fake('test')::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileStorage = new LaravelStorage($this->laravelStorageMock);
    }

    public function testSave()
    {
        $file = new File('init/test.txt', 'test.txt', 'test');
        $saveTo = 'newDirectory/new_test.txt';

        $this->laravelStorageMock->expects($this->once())
            ->method('put')
            ->with($saveTo, $file->content);

        $this->laravelStorageMock->expects($this->once())
            ->method('path')
            ->with($saveTo)
            ->willReturn('fullPath/' . $saveTo);

        $savedFile = $this->fileStorage->save($file, $saveTo);
        $this->assertSame($savedFile->fileName, $file->fileName);
        $this->assertSame($savedFile->content, $file->content);
        $this->assertSame($savedFile->fullPath, 'fullPath/' . $saveTo);
    }

    public function testDelete()
    {
        $filePath = 'test/test.txt';

        $this->laravelStorageMock->expects($this->once())
            ->method('exists')
            ->with($filePath)
            ->willReturn(true);

        $this->laravelStorageMock->expects($this->once())
            ->method('delete')
            ->with($filePath);

        $this->fileStorage->delete($filePath);
    }

    public function testDeleteIfDoesNotExists()
    {
        $filePath = 'test/test.txt';

        $this->laravelStorageMock->expects($this->once())
            ->method('exists')
            ->with($filePath)
            ->willReturn(false);

        $this->fileStorage->delete($filePath);
    }

    public function testExists()
    {
        $filePath = 'test/test.txt';

        $this->laravelStorageMock->expects($this->once())
            ->method('exists')
            ->with($filePath)
            ->willReturn(true);

        $this->assertTrue($this->fileStorage->exists($filePath));
    }

    public function testDoesNotExists()
    {
        $filePath = 'test/test.txt';

        $this->laravelStorageMock->expects($this->once())
            ->method('exists')
            ->with($filePath)
            ->willReturn(false);

        $this->assertFalse($this->fileStorage->exists($filePath));
    }

    public function testUrl()
    {
        $filePath = 'test/test.txt';

        $this->laravelStorageMock->expects($this->once())
            ->method('url')
            ->with($filePath)
            ->willReturn('localhost/' . $filePath);

        $this->assertSame(
            'localhost/' . $filePath,
            $this->fileStorage->url($filePath)
        );
    }
}