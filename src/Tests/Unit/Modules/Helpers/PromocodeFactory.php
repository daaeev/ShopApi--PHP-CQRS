<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;

trait PromocodeFactory
{
    private function makePromocode(
        PromocodeId $id,
        string $name,
        string $code,
        int $discountPercent,
        \DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate = null,
    ): Promocode {
        return new Promocode(
            $id,
            $name,
            $code,
            $discountPercent,
            $startDate,
            $endDate
        );
    }

    private function generatePromocode(): Promocode
    {
        $promocode = new Promocode(
            PromocodeId::random(),
            md5(rand()),
            md5(rand()),
            rand(1, 100),
            new \DateTimeImmutable('-' . rand(1, 10) . ' days'),
            new \DateTimeImmutable('+' . rand(1, 10) . ' days'),
        );
        $promocode->flushEvents();
        return $promocode;
    }
}