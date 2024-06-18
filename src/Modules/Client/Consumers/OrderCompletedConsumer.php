<?php

namespace Project\Modules\Client\Consumers;

use Project\Modules\Client\Entity\ClientId;
use Project\Modules\Shopping\Api\Events\Orders\OrderCompleted;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;

class OrderCompletedConsumer implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly ClientsRepositoryInterface $clients
    ) {}

    public function __invoke(OrderCompleted $event): void
    {
        $clientId = $event->getDTO()->client->client->getId();
        $client = $this->clients->get(ClientId::make($clientId));
        if ($client->getContacts()->isPhoneConfirmed()) {
            return;
        }

        $client->confirmPhone();
        $this->clients->update($client);
        $this->dispatchEvents($client->flushEvents());
    }
}