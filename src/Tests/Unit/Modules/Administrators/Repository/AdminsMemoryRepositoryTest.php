<?php

namespace Project\Tests\Unit\Modules\Administrators\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\IdentityMap;
use Project\Modules\Administrators\Repository\AdminsMemoryRepository;

class AdminsMemoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use AdminsRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->admins = new AdminsMemoryRepository(new Hydrator, new IdentityMap);
    }
}
