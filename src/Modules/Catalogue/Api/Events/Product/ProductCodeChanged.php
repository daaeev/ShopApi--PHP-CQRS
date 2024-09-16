<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

class ProductCodeChanged extends AbstractProductEvent
{
    public function getEventId(): string
    {
        return ProductEvent::CODE_CHANGED->value;
    }
}