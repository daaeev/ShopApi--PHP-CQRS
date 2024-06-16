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
        $this->expectNotToPerformAssertions();
        new Client(uniqid());
    }

    public function testSameWithEqualsHash()
    {
        $hash = uniqid();
        $client1 = new Client($hash);
        $client2 = new Client($hash);
        $this->assertTrue($client1->same($client2));
    }

    public function testSameWithEqualsHashAndId()
    {
        $hash = uniqid();
        $client1 = new Client($hash, 1);
        $client2 = new Client($hash, 1);
        $this->assertTrue($client1->same($client2));
    }

    public function testSameWithEqualsHashAndNotEqualsId()
    {
        $hash = uniqid();
        $client1 = new Client($hash, 1);
        $client2 = new Client($hash, 2);
        $this->assertTrue($client1->same($client2));
    }

    public function testSameWithNotEqualsHashAndEqualsId()
    {
        $client1 = new Client(uniqid(), 1);
        $client2 = new Client(uniqid(), 1);
        $this->assertTrue($client1->same($client2));
    }

    public function testNotSameClients()
    {
        $client1 = new Client(uniqid(), 1);
        $client2 = new Client(uniqid(), 2);
        $this->assertFalse($client1->same($client2));
    }
}