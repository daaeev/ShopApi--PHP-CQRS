<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;

class AdministratorNameTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    public function testUpdate()
    {
        $admin = $this->generateAdmin();
        $newName = 'New name for test update';
        $admin->setName($newName);
        $this->assertEmpty($admin->flushEvents());
        $this->assertSame($admin->getName(), $newName);
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateAdmin()->setName('');
    }
}