<?php

namespace Project\Tests\Unit\Modules\Administrators\Entity;

use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Administrators\Api\Events\AdminDeleted;

class DeleteTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory, AssertEvents;

    public function testDelete()
    {
        $admin = $this->generateAdmin();
        $admin->delete();
        $this->assertEvents($admin, [new AdminDeleted($admin)]);
    }
}