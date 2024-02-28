<?php

namespace Project\Tests\Unit\Modules\Administrators\Repository;

use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Entity\Admin;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Common\Repository\DuplicateKeyException;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Administrators\Repository\AdminsRepositoryInterface;

trait AdminsRepositoryTestTrait
{
    use AdminFactory;

    protected AdminsRepositoryInterface $admins;

    public function testAdd()
    {
        $initial = $this->generateAdmin();
        $name = $initial->getName();
        $login = $initial->getLogin();
        $password = $initial->getPassword();
        $roles = $initial->getRoles();

        $this->admins->add($initial);

        $found = $this->admins->get($initial->getId());
        $this->assertSame($initial, $found);
        $this->assertSame($found->getName(), $name);
        $this->assertSame($found->getLogin(), $login);
        $this->assertSame($found->getPassword(), $password);
        $this->assertSame($found->getRoles(), $roles);
    }

    public function testAddIncrementIds()
    {
        $admin = $this->makeAdmin(
            AdminId::next(),
            md5(rand()),
            $this->correctAdminLogin,
            $this->correctAdminPassword,
            [Role::ADMIN]
        );

        $this->admins->add($admin);
        $this->assertNotNull($admin->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $admin = $this->generateAdmin();
        $adminWithSameId = $this->makeAdmin(
            $admin->getId(),
            $admin->getName(),
            $this->correctAdminLogin,
            $admin->getPassword(),
            $admin->getRoles()
        );

        $this->admins->add($admin);
        $this->expectException(DuplicateKeyException::class);
        $this->admins->add($adminWithSameId);
    }

    public function testAddWithNotUniqueLogin()
    {
        $admin = $this->generateAdmin();
        $adminWithNotUniqueLogin = $this->generateAdmin();
        $adminWithNotUniqueLogin->setLogin($admin->getLogin());
        $this->admins->add($admin);
        $this->expectException(DuplicateKeyException::class);
        $this->admins->add($adminWithNotUniqueLogin);
    }

    public function testUpdate()
    {
        $initial = $this->generateAdmin();
        $this->admins->add($initial);
        $added = $this->admins->get($initial->getId());

        $added->setName('Updated admin name for test update');
        $added->setLogin($this->correctAdminLogin);
        $added->setPassword($this->correctAdminPassword);
        $added->setRoles([Role::MANAGER]);
        $this->admins->update($added);

        $updated = $this->admins->get($initial->getId());
        $this->assertSame($initial, $added);
        $this->assertSame($added, $updated);
        $this->assertSame($added->getName(), 'Updated admin name for test update');
        $this->assertSame($added->getLogin(), $this->correctAdminLogin);
        $this->assertSame($added->getPassword(), $this->correctAdminPassword);
        $this->assertSame($added->getRoles(), [Role::MANAGER]);
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
        $adminWithNotUniqueLogin = $this->generateAdmin();
        $this->admins->add($admin);
        $this->admins->add($adminWithNotUniqueLogin);
        $adminWithNotUniqueLogin->setLogin($admin->getLogin());
        $this->expectException(DuplicateKeyException::class);
        $this->admins->update($adminWithNotUniqueLogin);
    }

    public function testUpdateAdminAndDoesNotChangeLogin()
    {
        $admin = $this->generateAdmin();
        $this->admins->add($admin);
        $this->admins->update($admin);
        $this->expectNotToPerformAssertions();
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
        $initial = $this->generateAdmin();
        $this->admins->add($initial);
        $found = $this->admins->get($initial->getId());
        $this->assertSame($initial, $found);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->admins->get(AdminId::random());
    }
}
