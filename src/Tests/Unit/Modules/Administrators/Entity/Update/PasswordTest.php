<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity\Update;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Administrators\Api\Events\AdminPasswordChanged;

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory, AssertEvents;

    public function testUpdate()
    {
        $admin = $this->generateAdmin();
        $newPassword = $this->correctAdminPassword;
        $this->assertNotSame($admin->getPassword(), $newPassword);
        $admin->setPassword($newPassword);
        $this->assertEvents($admin, [new AdminPasswordChanged($admin)]);
        $this->assertSame($admin->getPassword(), $newPassword);
    }

    public function testUpdateToSame()
    {
        $admin = $this->generateAdmin();
        $admin->setPassword($admin->getPassword());
        $this->assertEmpty($admin->flushEvents());
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateAdmin()->setPassword('');
    }
}