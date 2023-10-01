<?php

namespace Project\Tests\Unit\Modules\Client\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Client\Repository\MemoryClientsRepository;

class MemoryClientRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use ClientRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->clients = new MemoryClientsRepository(new Hydrator);
        parent::setUp();
    }
}