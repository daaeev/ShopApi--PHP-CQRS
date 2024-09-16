<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

class ProductActivityChanged extends AbstractProductEvent
{
    public function getEventId(): string
    {
        return ProductEvent::ACTIVITY_CHANGED->value;
    }
}