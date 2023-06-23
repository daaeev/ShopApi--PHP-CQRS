<?php

namespace Project\Modules\Cart\Api\Events;

use Project\Common\Utils;
use Project\Common\Events\Event;
use Project\Modules\Cart\Entity;
use Project\Modules\Cart\Utils\Entity2DTOConverter;

class AbstractCartEvent extends Event
{
    public function __construct(
        private Entity\Cart $cart
    ) {}

    public function getDTO(): Utils\DTO
    {
        return Entity2DTOConverter::convert($this->cart);
    }
}