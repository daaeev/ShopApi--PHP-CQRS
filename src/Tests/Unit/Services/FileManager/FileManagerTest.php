<?php

namespace Project\Tests\Unit\Services\FileManager;

use PHPUnit\Framework\TestCase;
use Project\Common\Services\FileManager\File;
use Project\Common\Services\FileManager\FileManager;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Common\Services\FileManager\FileStorageInterface;
use Project\Common\Services\FileManager\FileNameGeneratorInterface;
use Project\Common\Services\FileManager\DirectoryNameGeneratorInterface;

class FileManagerTest extends TestCase
{
    private FileManagerInterface $fileManager;
    private FileNameGeneratorInterface $fileNameGeneratorMock;
    private DirectoryNameGeneratorInterface $directoryNameGeneratorMock;
    private FileStorageInterface $fileStorageMock;

    public function setUp(): void
    {
        $this->fileNameGeneratorMock = $this->getMockBuilder(FileNameGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->directoryNameGeneratorMock = $this->getMockBuilder(DirectoryNameGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileStorageMock = $this->getMockBuilder(FileStorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileManager = new FileManager(
            $this->directoryNameGeneratorMock,
            $this->fileNameGeneratorMock,
            $this->fileStorageMock
        );

        parent::setUp();
    }

    public function testSave()
    {
        $file = new File('init/test.txt', 'test.txt', 'test');
        $savedFile = new File('directory/new_test.txt', 'new_test.txt', 'test');

        $this->fileNameGeneratorMock->expects($this->once())
            ->method('generateName')
            ->with($file->fileName)
            ->willReturn('new_test.txt');

        $this->directoryNameGeneratorMock->expects($this->once())
            ->method('generateDirectoryName')
            ->with('new_test.txt')
            ->willReturn('directory');

        $this->fileStorageMock->expects($this->once())
            ->method('save')
            ->with($file, 'directory/new_test.txt')
            ->willReturn($savedFile);

        $this->assertSame($savedFile, $this->fileManager->save($file));
    }

    public function testDelete()
    {
        $fileName = 'test.txt';

        $this->directoryNameGeneratorMock->expects($this->once())
            ->method('generateDirectoryName')
            ->with($fileName)
            ->willReturn('directory');

        $this->fileStorageMock->expects($this->once())
            ->method('delete')
            ->with('directory/' . $fileName);

        $this->fileManager->delete($fileName);
    }

    public function testExists()
    {
        $fileName = 'test.txt';

        $this->directoryNameGeneratorMock->expects($this->once())
            ->method('generateDirectoryName')
            ->with($fileName)
            ->willReturn('directory');

        $this->fileStorageMock->expects($this->once())
            ->method('exists')
            ->with('directory/' . $fileName)
            ->willReturn(true);

        $this->assertTrue($this->fileManager->exists($fileName));
    }

    public function testDoesNotExists()
    {
        $fileName = 'test.txt';

        $this->directoryNameGeneratorMock->expects($this->once())
            ->method('generateDirectoryName')
            ->with($fileName)
            ->willReturn('directory');

        $this->fileStorageMock->expects($this->once())
            ->method('exists')
            ->with('directory/' . $fileName)
            ->willReturn(false);

        $this->assertFalse($this->fileManager->exists($fileName));
    }

    public function testUrl()
    {
        $fileName = 'test.txt';

        $this->directoryNameGeneratorMock->expects($this->once())
            ->method('generateDirectoryName')
            ->with($fileName)
            ->willReturn('directory');

        $this->fileStorageMock->expects($this->once())
            ->method('url')
            ->with('directory/' . $fileName)
            ->willReturn('localhost/directory/' . $fileName);

        $this->assertSame(
            'localhost/directory/' . $fileName,
            $this->fileManager->url($fileName),
        );
    }

    public function testUrlIfFileNameIsNull()
    {
        $this->assertNull($this->fileManager->url(null));
    }
}