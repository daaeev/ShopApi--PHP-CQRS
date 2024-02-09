<?php

namespace Project\Modules\Catalogue\Settings\Consumers;

use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Settings\Commands\UpdateProductSettingsCommand;
use Project\Modules\Catalogue\Settings\Services\CatalogueSettingsServiceInterface;
use Project\Modules\Catalogue\Api\Events\Product\ProductCreated as ProductCreatedEvent;

class ProductCreatedConsumer implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CatalogueSettingsServiceInterface $settings
    ) {}

    public function __invoke(ProductCreatedEvent $event)
    {
        $command = new UpdateProductSettingsCommand(
            $event->getDTO()->id,
            false
        );

        $this->settings->update($command);
    }
}