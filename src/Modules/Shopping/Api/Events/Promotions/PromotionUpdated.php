<?php

namespace Project\Modules\Shopping\Api\Events\Promotions;

class PromotionUpdated extends AbstractPromotionEvent
{
    public function getEventId(): string
    {
        return PromotionEvent::UPDATED->value;
    }
}