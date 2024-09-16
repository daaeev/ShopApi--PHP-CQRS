<?php

namespace Project\Modules\Shopping\Api\Events\Promocodes;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Discounts\Promocodes\Utils\PromocodeEntity2DTOConverter;

abstract class AbstractPromocodeEvent extends Event
{
    public function __construct(
        private Entity\Promocode $promocode,
    ) {}

    public function getData(): array
    {
        return PromocodeEntity2DTOConverter::convert($this->promocode)->toArray();
    }
}