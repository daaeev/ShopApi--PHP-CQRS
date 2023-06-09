<?php

namespace Project\Tests\Unit\Modules\Administrators\Repository;

use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Entity\Admin;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Common\Repository\DuplicateKeyException;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Administrators\Repository\AdminRepositoryInterface;

trait AdminRepositoryTestTrait
{
    use AdminFactory;

    protected AdminRepositoryInterface $admins;

    public function testAdd()
    {
        $initial = $this->generateAdmin();
        $this->admins->add($initial);
        $found = $this->admins->get($initial->getId());
        $this->assertSameAdmins($initial, $found);
    }

    private function assertSameAdmins(Admin $initial, Admin $found): void
    {
        $this->assertTrue($initial->getId()->equalsTo($found->getId()));
        $this->assertEquals($initial->getName(), $found->getName());
        $this->assertEquals($initial->getLogin(), $found->getLogin());
        $this->assertEquals($initial->getRoles(), $found->getRoles());
    }

    public function testAddIfAlreadyExists()
    {
        $admin = $this->generateAdmin();
        $secondAdmin = $this->makeAdmin(
            $admin->getId(),
            $admin->getName(),
            'Unique admin login',
            $admin->getPassword(),
            $admin->getRoles()
        );
        $this->admins->add($admin);
        $this->expectException(DuplicateKeyException::class);
        $this->admins->add($secondAdmin);
    }

    public function testAddWithNotUniqueLogin()
    {
        $admin = $this->generateAdmin();
        $adminWithNotUniqueCode = $this->generateAdmin();
        $adminWithNotUniqueCode->setLogin($admin->getLogin());
        $this->admins->add($admin);
        $this->expectException(DuplicateKeyException::class);
        $this->admins->add($adminWithNotUniqueCode);
    }

    public function testUpdate()
    {
        $initial = $this->generateAdmin();
        $this->admins->add($initial);
        $added = $this->admins->get($initial->getId());
        $added->setName('Updated admin name for test update');
        $added->setLogin('Updated admin login for test update');
        $added->setRoles([Role::MANAGER]);
        $this->admins->update($added);
        $updated = $this->admins->get($initial->getId());
        $this->assertSameAdmins($added, $updated);
        $this->assertNotSame($initial->getName(), $updated->getName());
        $this->assertNotSame($initial->getLogin(), $updated->getLogin());
        $this->assertNotSame($initial->getRoles(), $updated->getRoles());
    }

    public function testUpdateIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $admin = $this->generateAdmin();
        $this->admins->update($admin);
    }

    public function testUpdateWithNotUniqueLogin()
    {
        $admin = $this->generateAdmin();
        $adminWithNotUniqueCode = $this->generateAdmin();
        $this->admins->add($admin);
        $this->admins->add($adminWithNotUniqueCode);
        $adminWithNotUniqueCode->setLogin($admin->getLogin());
        $this->expectException(DuplicateKeyException::class);
        $this->admins->update($adminWithNotUniqueCode);
    }

    public function testDelete()
    {
        $admin = $this->generateAdmin();
        $this->admins->add($admin);
        $this->admins->delete($admin);
        $this->expectException(NotFoundException::class);
        $this->admins->get($admin->getId());
    }

    public function testDeleteIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $admin = $this->generateAdmin();
        $this->admins->delete($admin);
    }

    public function testGet()
    {
        $admin = $this->generateAdmin();
        $this->admins->add($admin);
        $found = $this->admins->get($admin->getId());
        $this->assertSameAdmins($admin, $found);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->admins->get(AdminId::random());
    }
}