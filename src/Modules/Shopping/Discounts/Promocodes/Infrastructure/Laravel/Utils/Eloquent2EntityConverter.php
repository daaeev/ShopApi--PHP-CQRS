<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models as Eloquent;

class Eloquent2EntityConverter
{
    public function __construct(
        private Hydrator $hydrator,
    ) {}

    public function convert(Eloquent\Promocode $promocode): Entity\Promocode
    {
        return $this->hydrator->hydrate(Entity\Promocode::class, [
            'id' => new Entity\PromocodeId($promocode->id),
            'name' => $promocode->name,
            'code' => $promocode->code,
            'discountPercent' => $promocode->discount_percent,
            'active' => $promocode->active,
            'startDate' => new \DateTimeImmutable($promocode->start_date),
            'endDate' => $promocode->end_date
                ? new \DateTimeImmutable($promocode->end_date)
                : null,
            'createdAt' => new \DateTimeImmutable($promocode->created_at),
            'updatedAt' => $promocode->updated_at
                ? new \DateTimeImmutable($promocode->updated_at)
                : null,
        ]);
    }
}