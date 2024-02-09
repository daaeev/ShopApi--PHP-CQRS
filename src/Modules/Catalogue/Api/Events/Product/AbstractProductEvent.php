<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

use Project\Modules\Catalogue\Product\Entity;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Modules\Catalogue\Product\Utils\ProductEntity2DTOConverter;

class AbstractProductEvent extends Event
{
    public function __construct(
        private Entity\Product $entity
    ) {}

    public function getDTO(): DTO\Product
    {
        return ProductEntity2DTOConverter::convert($this->entity);
    }
}