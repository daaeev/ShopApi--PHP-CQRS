<?php

namespace Project\Modules\Shopping\Adapters\Events;

use Webmozart\Assert\Assert;
use Project\Common\Product\Availability;
use Project\Common\ApplicationMessages\Events\SerializedEvent;
use Project\Modules\Catalogue\Api\Events\Product\ProductEvent;
use Project\Modules\Catalogue\Api\Events\Product\ProductActivityChanged;
use Project\Modules\Catalogue\Api\Events\Product\ProductAvailabilityChanged;

class ProductDeactivatedDeserializer
{
    public function __construct(
        private readonly SerializedEvent $event
    ) {
        $eventsId = [ProductActivityChanged::class, ProductAvailabilityChanged::class];
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