<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity;

use Project\Tests\Unit\Modules\Helpers\AdminFactory;

class CloneAdministratorTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    public function testClone()
    {
        $admin = $this->generateAdmin();
		$cloned = clone $admin;
		$this->assertNotSame($admin->getId(), $cloned->getId());
    }
}