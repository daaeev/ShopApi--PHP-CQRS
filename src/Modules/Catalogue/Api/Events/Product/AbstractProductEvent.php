<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

use Project\Modules\Catalogue\Product\Entity;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Catalogue\Product\Utils\ProductEntity2DTOConverter;

abstract class AbstractProductEvent extends Event
{
    public function __construct(
        private Entity\Product $entity
    ) {}

    public function getData(): array
    {
        return ProductEntity2DTOConverter::convert($this->entity)->toArray();
    }
}