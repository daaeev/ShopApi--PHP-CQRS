<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

use Project\Common\Utils;
use Project\Common\Events\Event;
use Project\Modules\Shopping\Cart\Entity;
use Project\Modules\Shopping\Cart\Utils\Entity2DTOConverter;

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