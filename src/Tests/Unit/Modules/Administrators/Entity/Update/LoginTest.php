<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity\Update;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Administrators\Api\Events\AdminLoginChanged;

class LoginTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory, AssertEvents;

    public function testUpdate()
    {
        $admin = $this->generateAdmin();
        $newLogin = $this->correctAdminLogin;
        $admin->setLogin($newLogin);
        $this->assertEvents($admin, [new AdminLoginChanged($admin)]);
        $this->assertSame($admin->getLogin(), $newLogin);
    }

    public function testUpdateToSame()
    {
        $admin = $this->generateAdmin();
        $admin->setLogin($admin->getLogin());
        $this->assertEmpty($admin->flushEvents());
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateAdmin()->setLogin('');
    }

    public function testLoginLengthValidation()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateAdmin()->setLogin('login');
    }

    public function testLoginWhitespacesValidation()
    {
        $this->expectException(\DomainException::class);
        $this->generateAdmin()->setLogin('Correct login');
    }
}