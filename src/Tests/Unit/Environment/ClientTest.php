<?php

namespace Project\Tests\Unit\Environment;

use Project\Common\Client\Client;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $client = new Client($hash = uniqid(), $id = rand());
        $this->assertSame($hash, $client->getHash());
        $this->assertSame($id, $client->getId());
    }

    public function testCreateWithEmptyHash()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Client('', rand());
    }

    public function testCreateWithEmptyId()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Client(uniqid(), 0);
    }
}