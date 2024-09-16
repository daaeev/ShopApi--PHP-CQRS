<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

class ProductUpdated extends AbstractProductEvent
{
    public function getEventId(): string
    {
        return ProductEvent::UPDATED->value;
    }
}