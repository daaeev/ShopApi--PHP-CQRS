<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity;

use Project\Common\Administrators\Role;
use Webmozart\Assert\InvalidArgumentException;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Modules\Administrators\Api\Events\AdminCreated;

class CreateAdministratorTest extends \PHPUnit\Framework\TestCase
{
    use AssertEvents, AdminFactory;

    public function testCreate()
    {
        $admin = $this->makeAdmin(
            $id = AdminId::random(),
            $name = uniqid(),
            $login = uniqid(),
            $password = uniqid(),
            $roles = [Role::ADMIN, Role::MANAGER],
        );

        $this->assertTrue($id->equalsTo($admin->getId()));
        $this->assertSame($name, $admin->getName());
        $this->assertSame($login, $admin->getLogin());
        $this->assertSame($password, $admin->getPassword());
        $this->assertSame($roles, $admin->getRoles());
        $this->assertEvents($admin, [new AdminCreated($admin)]);
    }

    public function testCreateWithEmptyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeAdmin(
            AdminId::next(),
            '',
            uniqid(),
            uniqid(),
            [Role::ADMIN, Role::MANAGER],
        );
    }

    public function testCreateWithEmptyLogin()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            '',
            uniqid(),
            [Role::ADMIN, Role::MANAGER],
        );
    }

    public function testCreateWithIncorrectLoginLength()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            'login',
            uniqid(),
            [Role::ADMIN, Role::MANAGER],
        );
    }

    public function testCreateWithLoginWithWhitespace()
    {
        $this->expectException(\DomainException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            'Correct login',
            uniqid(),
            [Role::ADMIN, Role::MANAGER],
        );
    }

    public function testCreateWithEmptyPassword()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            uniqid(),
            '',
            [Role::ADMIN, Role::MANAGER],
        );
    }

    public function testCreateWithIncorrectPasswordLength()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            uniqid(),
            'pass',
            [Role::ADMIN, Role::MANAGER],
        );
    }

    public function testCreateWithPasswordWithWhitespaces()
    {
        $this->expectException(\DomainException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            uniqid(),
            'Correct password',
            [Role::ADMIN, Role::MANAGER],
        );
    }

    public function testCreateWithoutRoles()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            uniqid(),
            uniqid(),
            [],
        );
    }

    public function testCreateWithInvalidRoles()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeAdmin(
            AdminId::next(),
            uniqid(),
            uniqid(),
            uniqid(),
            ['Invalid role'],
        );
    }
}