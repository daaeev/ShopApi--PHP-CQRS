<?php

namespace Project\Tests\Unit\Modules\Client\Consumers;

use PHPUnit\Framework\TestCase;
use Project\Modules\Client\Entity\Client;
use Project\Modules\Client\Entity\ClientId;
use Project\Modules\Client\Entity\Contacts;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Client\Consumers\OrderCompletedConsumer;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Client\Adapters\Events\OrderCompletedDeserializer;

class OrderCompletedConsumerTest extends TestCase
{
    private readonly ClientsRepositoryInterface $clients;
    private readonly MessageBusInterface $eventBus;
    private readonly OrderCompletedConsumer $consumer;

    private readonly OrderCompletedDeserializer $deserializer;
    private readonly int $clientId;
    private readonly Client $client;
    private readonly Contacts $contacts;
    private readonly Event $clientUpdatedEvent;

    protected function setUp(): void
    {
        $this->clients = $this->getMockBuilder(ClientsRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $this->consumer = new OrderCompletedConsumer($this->clients);
        $this->consumer->setDispatcher($this->eventBus);

        $this->deserializer = $this->getMockBuilder(OrderCompletedDeserializer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientId = random_int(1, 100);
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contacts = $this->getMockBuilder(Contacts::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientUpdatedEvent = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testOrderCompletedEvent()
    {
        $this->deserializer->expects($this->once())
            ->method('getClientId')
            ->willReturn($this->clientId);

        $this->clients->expects($this->once())
            ->method('get')
            ->with(ClientId::make($this->clientId))
            ->willReturn($this->client);

        $this->client->expects($this->once())
            ->method('getContacts')
            ->willReturn($this->contacts);

        $this->contacts->expects($this->once())
            ->method('isPhoneConfirmed')
            ->willReturn(false);

        $this->client->expects($this->once())->method('confirmPhone');

        $this->clients->expects($this->once())
            ->method('update')
            ->with($this->client);

        $this->client->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->clientUpdatedEvent]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->clientUpdatedEvent);

        call_user_func($this->consumer, $this->deserializer);
    }

    public function testOrderCompletedEventIfClientPhoneAlreadyConfirmed()
    {
        $this->deserializer->expects($this->once())
            ->method('getClientId')
            ->willReturn($this->clientId);

        $this->clients->expects($this->once())
            ->method('get')
            ->with(ClientId::make($this->clientId))
            ->willReturn($this->client);

        $this->client->expects($this->once())
            ->method('getContacts')
            ->willReturn($this->contacts);

        $this->contacts->expects($this->once())
            ->method('isPhoneConfirmed')
            ->willReturn(true);

        call_user_func($this->consumer, $this->deserializer);
    }
}