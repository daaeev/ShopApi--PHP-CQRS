<?php

namespace Project\Modules\Shopping\Api\Events\Promotions;

class PromotionCreated extends AbstractPromotionEvent
{
    public function getEventId(): string
    {
        return PromotionEvent::CREATED->value;
    }
}