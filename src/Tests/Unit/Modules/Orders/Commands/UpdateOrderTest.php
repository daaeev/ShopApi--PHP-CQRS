<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Entity\ClientInfo;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Modules\Shopping\Order\Entity\PaymentStatus;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;
use Project\Modules\Shopping\Order\Commands\UpdateOrderCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\UpdateOrderHandler;
use Project\Modules\Shopping\Api\DTO\Order\DeliveryInfo as DeliveryInfoDTO;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryInfo as DeliveryInfoEntity;

class UpdateOrderTest extends \PHPUnit\Framework\TestCase
{
    use ContactsGenerator;

    private readonly OrdersRepositoryInterface $orders;
    private readonly MessageBusInterface $eventBus;

    private readonly OrderId $orderId;
    private readonly Order $order;
    private readonly ClientInfo $clientInfo;
    private readonly Client $client;
    private readonly Event $event;

    protected function setUp(): void
    {
        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->orderId = OrderId::random();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientInfo = $this->getMockBuilder(ClientInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testUpdate()
    {
        $command = $this->getCommand();
        $this->mockOrderEntityMethods($command);

        $this->order->expects($this->once())
            ->method('getPaymentStatus')
            ->willReturn(PaymentStatus::NOT_PAID);

        $this->order->expects($this->once())
            ->method('updatePaymentStatus')
            ->with(PaymentStatus::from($command->paymentStatus));

        $this->order->expects($this->once())
            ->method('getStatus')
            ->willReturn(OrderStatus::NEW);

        $this->order->expects($this->once())
            ->method('updateStatus')
            ->with(OrderStatus::from($command->status));

        $handler = new UpdateOrderHandler($this->orders);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }

    private function getCommand(): UpdateOrderCommand
    {
        return new UpdateOrderCommand(
            id: $this->orderId->getId(),
            firstName: uniqid(),
            lastName: uniqid(),
            phone: $this->generatePhone(),
            email: $this->generateEmail(),
            status: OrderStatus::IN_PROGRESS->value,
            paymentStatus: PaymentStatus::PAID->value,
            delivery: new DeliveryInfoDTO(
                service: DeliveryService::NOVA_POST->value,
                country: uniqid(),
                city: uniqid(),
                street: uniqid(),
                houseNumber: uniqid(),
            ),
            managerComment: uniqid()
        );
    }

    private function mockOrderEntityMethods(UpdateOrderCommand $command): void
    {
        $this->orders->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('getClient')
            ->willReturn($this->clientInfo);

        $this->clientInfo->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->mockUpdateClientInfo($command);
        $this->mockUpdateDeliveryInfo($command);

        $this->order->expects($this->once())
            ->method('updateManagerComment')
            ->with($command->managerComment);

        $this->orders->expects($this->once())
            ->method('update')
            ->with($this->order);

        $this->mockEventBus();
    }

    private function mockUpdateClientInfo(UpdateOrderCommand $command): void
    {
        $this->order->expects($this->once())
            ->method('updateClientInfo')
            ->with($this->callback(function (ClientInfo $clientInfo) use ($command) {
                $this->assertSame($this->client, $clientInfo->getClient());
                $this->assertSame($command->firstName, $clientInfo->getFirstName());
                $this->assertSame($command->lastName, $clientInfo->getLastName());
                $this->assertSame($command->phone, $clientInfo->getPhone());
                $this->assertSame($command->email, $clientInfo->getEmail());
                return true;
            }));
    }

    private function mockUpdateDeliveryInfo(UpdateOrderCommand $command): void
    {
        $this->order->expects($this->once())
            ->method('updateDelivery')
            ->with($this->callback(function (DeliveryInfoEntity $deliveryInfo) use ($command) {
                $this->assertSame(DeliveryService::from($command->delivery->service), $deliveryInfo->getService());
                $this->assertSame($command->delivery->country, $deliveryInfo->getCountry());
                $this->assertSame($command->delivery->city, $deliveryInfo->getCity());
                $this->assertSame($command->delivery->street, $deliveryInfo->getStreet());
                $this->assertSame($command->delivery->houseNumber, $deliveryInfo->getHouseNumber());
                return true;
            }));
    }

    private function mockEventBus(): void
    {
        $this->order->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->event]);

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->event);
    }

    public function testUpdateWithSameStatuses()
    {
        $command = $this->getCommand();
        $this->mockOrderEntityMethods($command);

        $this->order->expects($this->once())
            ->method('getPaymentStatus')
            ->willReturn(PaymentStatus::from($command->paymentStatus));

        $this->order->expects($this->never())->method('updatePaymentStatus');

        $this->order->expects($this->once())
            ->method('getStatus')
            ->willReturn(OrderStatus::from($command->status));

        $this->order->expects($this->never())->method('updateStatus');

        $handler = new UpdateOrderHandler($this->orders);
        $handler->setDispatcher($this->eventBus);
        call_user_func($handler, $command);
    }
}