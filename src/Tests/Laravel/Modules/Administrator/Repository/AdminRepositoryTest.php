<?php

namespace Project\Tests\Laravel\Modules\Administrator\Repository;

use Illuminate\Contracts\Hashing\Hasher;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Administrators\Repository\AdminRepositoryTestTrait;
use Project\Modules\Administrators\Infrastructure\Laravel\Repository\AdminsRepository;

class AdminRepositoryTest extends \Tests\TestCase
{
    use AdminRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admins = new AdminsRepository(
            new Hydrator,
            $this->app->make(Hasher::class),
        );
    }
}