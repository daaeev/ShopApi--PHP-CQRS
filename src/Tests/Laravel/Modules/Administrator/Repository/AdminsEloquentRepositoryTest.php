<?php

namespace Project\Tests\Laravel\Modules\Administrator\Repository;

use Illuminate\Contracts\Hashing\Hasher;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Administrators\Repository\AdminsRepositoryTestTrait;
use Project\Modules\Administrators\Infrastructure\Laravel\Repository\AdminsEloquentRepository;

class AdminsEloquentRepositoryTest extends \Tests\TestCase
{
    use AdminsRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admins = new AdminsEloquentRepository(
            new Hydrator,
            $this->app->make(Hasher::class),
        );
    }
}