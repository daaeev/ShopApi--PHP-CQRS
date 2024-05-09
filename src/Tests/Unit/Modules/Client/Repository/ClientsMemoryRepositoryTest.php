<?php

namespace Project\Tests\Unit\Modules\Client\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Client\Repository\ClientsMemoryRepository;

class ClientsMemoryRepositoryTest extends \PHPUnit\Framework\TestCase
{
    use ClientsRepositoryTestTrait;

    protected function setUp(): void
    {
        $this->clients = new ClientsMemoryRepository(new Hydrator, new IdentityMap);
    }
}
