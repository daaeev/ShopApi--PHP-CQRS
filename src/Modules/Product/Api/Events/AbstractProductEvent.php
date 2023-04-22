<?php

namespace Project\Modules\Product\Api\Events;

use Project\Modules\Product\Api\DTO;

class AbstractProductEvent extends \Project\Common\Events\Event
{
    public function __construct(
        private DTO\Product $dto
    ) {}

    public function getDTO(): DTO\Product
    {
        return $this->dto;
    }
}