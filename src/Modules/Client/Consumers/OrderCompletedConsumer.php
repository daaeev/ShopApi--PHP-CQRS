<?php

namespace Project\Modules\Client\Consumers;

use Project\Modules\Client\Entity\ClientId;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Client\Adapters\Events\OrderCompletedDeserializer;

class OrderCompletedConsumer implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly ClientsRepositoryInterface $clients
    ) {}

    public function __invoke(OrderCompletedDeserializer $event): void
    {
        $client = $this->clients->get(ClientId::make($event->getClientId()));
        if ($client->getContacts()->isPhoneConfirmed()) {
            return;
        }

        $client->confirmPhone();
        $this->clients->update($client);
        $this->dispatchEvents($client->flushEvents());
    }
}