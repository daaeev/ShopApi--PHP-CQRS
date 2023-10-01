<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity\Update;

use Project\Common\Administrators\Role;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Administrators\Api\Events\AdminRolesChanged;

class RolesTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory, AssertEvents;

    public function testUpdate()
    {
        $admin = $this->generateAdmin();
        $newRoles = [Role::MANAGER];
        $admin->setRoles($newRoles);
        $this->assertEvents($admin, [new AdminRolesChanged($admin)]);
        $this->assertSame($admin->getRoles(), $newRoles);
    }

    public function testUpdateToSame()
    {
        $admin = $this->generateAdmin();
        $admin->setRoles($admin->getRoles());
        $this->assertEmpty($admin->flushEvents());
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateAdmin()->setRoles([]);
    }

    public function testUpdateWithInvalidRole()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateAdmin()->setRoles(['Invalid role']);
    }

    public function testHasAccess()
    {
        $admin = $this->generateAdmin();
        $this->assertTrue($admin->hasAccess(Role::ADMIN));
        $this->assertTrue($admin->hasAccess(Role::MANAGER));
        $admin->setRoles([Role::MANAGER]);
        $this->assertFalse($admin->hasAccess(Role::ADMIN));
        $this->assertTrue($admin->hasAccess(Role::MANAGER));
    }
}