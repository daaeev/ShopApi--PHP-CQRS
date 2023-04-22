<?php

namespace Project\Modules\Product\Api\Events;

use Project\Modules\Product\Api\DTO;
use Project\Modules\Product\Entity;
use Project\Modules\Product\Utils\Entity2DTOConverter;

class AbstractProductEvent extends \Project\Common\Events\Event
{
    public function __construct(
        private Entity\Product $entity
    ) {}

    public function getDTO(): DTO\Product
    {
        return Entity2DTOConverter::convert($this->entity);
    }
}