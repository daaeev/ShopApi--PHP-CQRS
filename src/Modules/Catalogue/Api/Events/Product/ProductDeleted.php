<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

class ProductDeleted extends AbstractProductEvent
{
    public function getEventId(): string
    {
        return ProductEvent::DELETED->value;
    }
}