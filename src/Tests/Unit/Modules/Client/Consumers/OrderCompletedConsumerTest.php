<?php

namespace Project\Tests\Unit\Modules\Client\Consumers;

use PHPUnit\Framework\TestCase;
use Project\Modules\Client\Entity\Client;
use Project\Modules\Client\Entity\ClientId;
use Project\Modules\Client\Entity\Contacts;
use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Client\Consumers\OrderCompletedConsumer;
use Project\Modules\Shopping\Api\Events\Orders\OrderCompleted;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Utils\OrderEntityToDTOConverter;

class OrderCompletedConsumerTest extends TestCase
{
    use OffersFactory, OrderFactory;

    private readonly ClientsRepositoryInterface $clients;
    private readonly MessageBusInterface $eventBus;
    private readonly OrderCompletedConsumer $consumer;

    private readonly OrderCompleted $orderCompletedEvent;
    private readonly Client $client;
    private readonly Contacts $contacts;
    private readonly Event $clientUpdatedEvent;

    protected function setUp(): void
    {
        $this->clients = $this->getMockBuilder(ClientsRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $this->consumer = new OrderCompletedConsumer($this->clients);
        $this->consumer->setDispatcher($this->eventBus);

        $this->orderCompletedEvent = $this->getMockBuilder(OrderCompleted::class)
            ->disableOriginalConstructor()
            ->getMock();

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
        $order = $this->generateOrder([$this->generateOffer()]);
        $orderDTO = OrderEntityToDTOConverter::convert($order);
        $this->orderCompletedEvent->expects($this->once())
            ->method('getDTO')
            ->willReturn($orderDTO);

        $this->clients->expects($this->once())
            ->method('get')
            ->with(ClientId::make($orderDTO->client->client->getId()))
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

        call_user_func($this->consumer, $this->orderCompletedEvent);
    }

    public function testOrderCompletedEventIfClientPhoneAlreadyConfirmed()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $orderDTO = OrderEntityToDTOConverter::convert($order);
        $this->orderCompletedEvent->expects($this->once())
            ->method('getDTO')
            ->willReturn($orderDTO);

        $this->clients->expects($this->once())
            ->method('get')
            ->with(ClientId::make($orderDTO->client->client->getId()))
            ->willReturn($this->client);

        $this->client->expects($this->once())
            ->method('getContacts')
            ->willReturn($this->contacts);

        $this->contacts->expects($this->once())
            ->method('isPhoneConfirmed')
            ->willReturn(true);

        call_user_func($this->consumer, $this->orderCompletedEvent);
    }
}