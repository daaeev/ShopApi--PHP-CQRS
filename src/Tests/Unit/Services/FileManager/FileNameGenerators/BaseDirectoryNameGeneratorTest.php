<?php

namespace Project\Tests\Unit\Services\FileManager\FileNameGenerators;

use PHPUnit\Framework\TestCase;
use Project\Common\Services\FileManager\DirectoryNameGenerators\BaseDirectoryNameGenerator;

class BaseDirectoryNameGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $directoryName = 'test';
        $generator = new BaseDirectoryNameGenerator($directoryName);
        $generatedDirectoryName = $generator->generateDirectoryName('test.txt');
        $this->assertSame($directoryName, $generatedDirectoryName);
    }
}