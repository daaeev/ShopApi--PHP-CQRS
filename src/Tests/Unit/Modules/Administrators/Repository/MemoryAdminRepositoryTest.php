<?php

namespace Project\Tests\Unit\Modules\Administrators\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Administrators\Repository\MemoryAdminRepository;

class MemoryAdminRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use AdminRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->admins = new MemoryAdminRepository(new Hydrator);
        parent::setUp();
    }
}