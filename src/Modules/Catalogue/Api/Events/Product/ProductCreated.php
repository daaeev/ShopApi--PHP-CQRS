<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

class ProductCreated extends AbstractProductEvent
{
    public function getEventId(): string
    {
        return ProductEvent::CREATED->value;
    }
}