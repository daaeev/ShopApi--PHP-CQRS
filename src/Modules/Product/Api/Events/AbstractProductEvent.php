<?php

namespace Project\Modules\Product\Api\Events;

use Project\Modules\Product\Api\DTO;
use Project\Modules\Product\Entity;
use Project\Modules\Product\Utils\Entity2DTOConverter;
use Project\Common\Events\Event;

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