<?php

namespace Project\Tests\Laravel\Modules\Promocodes\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Project\Tests\Unit\Modules\Promocodes\Repository\PromocodesRepositoryTestTrait;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository\PromocodeRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\Eloquent2EntityConverter;

class PromocodeRepositoryTest extends \Tests\TestCase
{
    use PromocodesRepositoryTestTrait, RefreshDatabase;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodeRepository(
            new Hydrator,
            new Eloquent2EntityConverter(new Hydrator)
        );
        parent::setUp();
    }
}