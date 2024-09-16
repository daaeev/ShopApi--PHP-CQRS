<?php

namespace Project\Modules\Shopping\Api\Events\Promotions;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Utils\PromotionEntity2DTOConverter;

abstract class AbstractPromotionEvent extends Event
{
    public function __construct(
        private Entity\Promotion $promotion,
    ) {}

    public function getData(): array
    {
        return PromotionEntity2DTOConverter::convert($this->promotion)->toArray();
    }
}