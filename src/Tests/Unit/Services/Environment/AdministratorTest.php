<?php

namespace Project\Tests\Unit\Services\Environment;

use Project\Common\Services\Environment\Administrator;

class AdministratorTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $admin = new Administrator($id = rand(), $name = uniqid());
        $this->assertSame($id, $admin->getId());
        $this->assertSame($name, $admin->getName());
    }

    public function testCreateWithEmptyId()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Administrator(id: 0, name: uniqid());
    }

    public function testSame()
    {
        $admin = new Administrator(id: rand(), name: uniqid());
        $sameAdmin = new Administrator(id: $admin->getId(), name: $admin->getName());
        $this->assertTrue($admin->same($sameAdmin));
    }

    public function testSameWithNotSameNames()
    {
        $admin = new Administrator(id: rand(), name: uniqid());
        $sameAdmin = new Administrator(id: $admin->getId(), name: uniqid());
        $this->assertTrue($admin->same($sameAdmin));
    }

    public function testNotSame()
    {
        $admin = new Administrator(id: rand(), name: uniqid());
        $sameAdmin = new Administrator(id: $admin->getId() + rand(), name: uniqid());
        $this->assertFalse($admin->same($sameAdmin));
    }
}