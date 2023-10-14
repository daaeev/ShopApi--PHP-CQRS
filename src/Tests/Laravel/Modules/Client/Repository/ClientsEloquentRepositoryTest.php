<?php

namespace Client\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Client\Repository\ClientsRepositoryTestTrait;
use Project\Modules\Client\Infrastructure\Laravel\Repository\ClientsEloquentRepository;

class ClientsEloquentRepositoryTest extends \Tests\TestCase
{
    use ClientsRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->clients = new ClientsEloquentRepository(new Hydrator);
        parent::setUp();
    }
}