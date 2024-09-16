<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

class ProductAvailabilityChanged extends AbstractProductEvent
{
    public function getEventId(): string
    {
        return ProductEvent::AVAILABILITY_CHANGED->value;
    }
}