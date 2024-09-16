<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

class ProductPricesChanged extends AbstractProductEvent
{
    public function getEventId(): string
    {
        return ProductEvent::PRICES_CHANGED->value;
    }
}