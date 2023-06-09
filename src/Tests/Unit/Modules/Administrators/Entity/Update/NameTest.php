<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity\Update;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;

class NameTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    public function testUpdate()
    {
        $admin = $this->generateAdmin();
        $newName = 'New name for test update';
        $this->assertNotSame($admin->getName(), $newName);
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