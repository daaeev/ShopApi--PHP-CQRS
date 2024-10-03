<?php

namespace Project\Modules\Shopping\Adapters\Events;

use Webmozart\Assert\Assert;
use Project\Common\Product\Availability;
use Project\Common\ApplicationMessages\Events\SerializedEvent;
use Project\Modules\Catalogue\Api\Events\Product\ProductEvent;

class ProductDeactivatedDeserializer
{
    public function __construct(
        private readonly SerializedEvent $event
    ) {
        $eventsId = [ProductEvent::ACTIVITY_CHANGED->value, ProductEvent::AVAILABILITY_CHANGED->value];
        Assert::inArray($event->getEventId(), $eventsId);
    }

    public function activityChanged(): bool
    {
        return ProductEvent::from($this->event->getEventId()) === ProductEvent::ACTIVITY_CHANGED;
    }

    public function getProductId(): int
    {
        return $this->event->id;
    }

    public function isProductActive(): bool
    {
        return $this->event->active;
    }

    public function isProductAvailable(): bool
    {
        return Availability::from($this->event->availability) !== Availability::OUT_STOCK;
    }
}