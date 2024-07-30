<?php

namespace Project\Tests\Laravel\Modules\Administrator\Repository;

use Illuminate\Contracts\Hashing\Hasher;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Administrators\Repository\AdminsRepositoryTestTrait;
use Project\Modules\Administrators\Infrastructure\Laravel\Repository\AdminsEloquentRepository;

class AdminsEloquentRepositoryTest extends \Project\Tests\Laravel\TestCase
{
    use AdminsRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admins = new AdminsEloquentRepository(
            new Hydrator,
            new IdentityMap,
            $this->app->make(Hasher::class),
        );
    }
}
