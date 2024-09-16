<?php

namespace Project\Modules\Shopping\Api\Events\Promocodes;

class PromocodeCreated extends AbstractPromocodeEvent
{
    public function getEventId(): string
    {
        return PromocodeEvent::CREATED->value;
    }
}