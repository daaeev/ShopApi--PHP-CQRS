<?php

namespace Project\Modules\Catalogue\Product\Api\Events;

use Project\Common\Events\Event;
use Project\Modules\Catalogue\Product\Entity;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Modules\Catalogue\Product\Utils\Entity2DTOConverter;

class AbstractProductEvent extends Event
{
    public function __construct(
        private Entity\Product $entity
    ) {}

    public function getDTO(): DTO\Product
    {
        return Entity2DTOConverter::convert($this->entity);
    }
}