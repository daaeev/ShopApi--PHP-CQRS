<?php

namespace Project\Tests\Unit\Services\FileManager\FileNameGenerators;

use PHPUnit\Framework\TestCase;
use Project\Common\Services\FileManager\FileNameGenerators\TimestampPrefixFileNameGenerator;

class TimestampPrefixFileNameGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new TimestampPrefixFileNameGenerator;
        $fileName = 'test.txt';
        $generatedFileName = $generator->generateName($fileName);
        $currentTime = time();
        list($timestamp, $initFileName) = explode('_', $generatedFileName, 2);
        $this->assertSame($fileName, $initFileName);
        $this->assertSame(mb_strlen($currentTime), mb_strlen($timestamp));
    }
}