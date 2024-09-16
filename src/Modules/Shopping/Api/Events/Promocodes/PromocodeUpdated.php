<?php

namespace Project\Modules\Shopping\Api\Events\Promocodes;

class PromocodeUpdated extends AbstractPromocodeEvent
{
    public function getEventId(): string
    {
        return PromocodeEvent::UPDATED->value;
    }
}