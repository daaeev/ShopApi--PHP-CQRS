<?php

namespace Project\Modules\Shopping\Api\Events\Promocodes;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Api\DTO\Promocodes as DTO;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Discounts\Promocodes\Utils\PromocodeEntity2DTOConverter;

class AbstractPromocodeEvent extends Event
{
    public function __construct(
        private Entity\Promocode $promocode,
    ) {}

    public function getDTO(): DTO\Promocode
    {
        return PromocodeEntity2DTOConverter::convert($this->promocode);
    }
}