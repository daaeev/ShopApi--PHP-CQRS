<?php

namespace Project\Modules\Shopping\Api\Events\Promotions;

class PromotionDeleted extends AbstractPromotionEvent
{
    public function getEventId(): string
    {
        return PromotionEvent::DELETED->value;
    }
}