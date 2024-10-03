<?php

namespace Project\Modules\Client\Adapters\Events;

use Webmozart\Assert\Assert;
use Project\Modules\Shopping\Api\Events\Orders\OrderEvent;
use Project\Common\ApplicationMessages\Events\SerializedEvent;

class OrderCompletedDeserializer
{
    public function __construct(
        private readonly SerializedEvent $event
    ) {
        Assert::same($this->event->getEventId(), OrderEvent::COMPLETED->value);
    }

    public function getClientId(): int
    {
        return $this->event->client['id'];
    }
}