<?php

namespace Project\Tests\Unit\Services\Environment;

use Project\Common\Administrators\Role;
use Project\Common\Services\Environment\Administrator;

class AdministratorTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $admin = new Administrator($id = rand(), $name = uniqid(), $roles = [Role::ADMIN]);
        $this->assertSame($id, $admin->getId());
        $this->assertSame($name, $admin->getName());
        $this->assertSame($roles, $admin->getRoles());
    }

    public function testCreateWithoutRoles()
    {
        $admin = new Administrator(id: rand(), name: uniqid());
        $this->assertEmpty($admin->getRoles());
    }

    public function testCreateWithEmptyId()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Administrator(id: 0, name: uniqid());
    }

    public function testHasAccess()
    {
        $admin = new Administrator(id: rand(), name: uniqid(), roles: [Role::ADMIN]);
        $this->assertTrue($admin->hasAccess(Role::ADMIN));
        $this->assertTrue($admin->hasAccess(Role::MANAGER));

        $admin = new Administrator(id: rand(), name: uniqid(), roles: [Role::MANAGER]);
        $this->assertFalse($admin->hasAccess(Role::ADMIN));
        $this->assertTrue($admin->hasAccess(Role::MANAGER));
    }

    public function testSame()
    {
        $admin = new Administrator(id: rand(), name: uniqid());
        $sameAdmin = new Administrator(id: $admin->getId(), name: uniqid(), roles: [Role::ADMIN]);
        $this->assertTrue($admin->same($sameAdmin));
    }

    public function testNotSame()
    {
        $admin = new Administrator(id: rand(), name: uniqid(), roles: [Role::ADMIN]);
        $sameAdmin = new Administrator(id: $admin->getId() + rand(), name: $admin->getName(), roles: $admin->getRoles());
        $this->assertFalse($admin->same($sameAdmin));
    }
}