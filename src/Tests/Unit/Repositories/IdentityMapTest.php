<?php

namespace Project\Tests\Unit\Repositories;

use PHPUnit\Framework\TestCase;
use Project\Common\Entity\Aggregate;
use Project\Common\Repository\IdentityMap;

class IdentityMapTest extends TestCase
{
    public function testAddObject()
    {
        $identityMap = new IdentityMap;
        $object = $this->getMockBuilder(Aggregate::class)->getMock();
        $identityMap->add('test', $object);
        $sameObject = $identityMap->get('test');
        $this->assertSame($object, $sameObject);
    }

    public function testAddObjectIfKeyAlreadyExists()
    {
        $identityMap = new IdentityMap;
        $object = $this->getMockBuilder(Aggregate::class)->getMock();
        $identityMap->add('test', $object);
        $this->expectException(\DomainException::class);
        $identityMap->add('test', $object);
    }

    public function testHasObject()
    {
        $identityMap = new IdentityMap;
        $object = $this->getMockBuilder(Aggregate::class)->getMock();
        $this->assertFalse($identityMap->has('test'));
        $identityMap->add('test', $object);
        $this->assertTrue($identityMap->has('test'));
    }

    public function testGetIfKeyDoesNotExists()
    {
        $identityMap = new IdentityMap;
        $this->expectException(\DomainException::class);
        $identityMap->get('test');
    }

    public function testRemoveObject()
    {
        $identityMap = new IdentityMap;
        $object = $this->getMockBuilder(Aggregate::class)->getMock();
        $identityMap->add('test', $object);
        $identityMap->remove('test');
        $this->assertFalse($identityMap->has('test'));
    }

    public function testRemoveObjectIfKeyDoesNotExists()
    {
        $identityMap = new IdentityMap;
        $this->expectException(\DomainException::class);
        $identityMap->remove('test');
    }
}