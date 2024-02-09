<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

use Project\Common\Utils;
use Project\Modules\Shopping\Cart\Entity;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Cart\Utils\CartEntity2DTOConverter;

class AbstractCartEvent extends Event
{
    public function __construct(
        private Entity\Cart $cart
    ) {}

    public function getDTO(): Utils\DTO
    {
        return CartEntity2DTOConverter::convert($this->cart);
    }
}