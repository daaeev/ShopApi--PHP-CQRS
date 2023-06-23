<?php

namespace Project\Modules\Cart\Api\Events;

use Project\Modules\Cart\Entity;
use Project\Modules\Cart\Api\DTO;
use Project\Modules\Cart\Utils\Entity2DTOConverter;

class AbstractCartItemEvent extends AbstractCartEvent
{
    private Entity\CartItem $cartItem;

    public function __construct(Entity\Cart $cart, Entity\CartItem $cartItem)
    {
        $this->cartItem = $cartItem;
        parent::__construct($cart);
    }

    public function getCartItem(): DTO\CartItem
    {
        return Entity2DTOConverter::convertCartItem($this->cartItem);
    }
}