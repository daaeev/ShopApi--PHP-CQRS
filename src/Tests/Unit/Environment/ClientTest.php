<?php

namespace Project\Tests\Unit\Environment;

use Project\Common\Environment\Client\Client;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $client = new Client(
            $hash = md5(rand()),
            $id = rand()
        );
        $this->assertSame($hash, $client->getHash());
        $this->assertSame($id, $client->getId());
    }

    public function testCreateWithEmptyHash()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Client(
            '',
            rand()
        );
    }

    public function testCreateWithEmptyId()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Client(
            md5(rand()),
            0
        );
    }
}