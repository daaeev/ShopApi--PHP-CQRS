<?php

namespace Project\Modules\Shopping\Api\Events\Promotions;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Api\DTO\Promotions as DTO;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Utils\PromotionEntity2DTOConverter;

class AbstractPromotionEvent extends Event
{
    public function __construct(
        private Entity\Promotion $promotion,
    ) {}

    public function getDTO(): DTO\Promotion
    {
        return PromotionEntity2DTOConverter::convert($this->promotion);
    }
}