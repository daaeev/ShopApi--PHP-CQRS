<?php

namespace Project\Modules\Shopping\Api\Events\Promocodes;

class PromocodeDeleted extends AbstractPromocodeEvent
{
    public function getEventId(): string
    {
        return PromocodeEvent::DELETED->value;
    }
}